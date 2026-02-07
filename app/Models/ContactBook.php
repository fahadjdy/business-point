<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ContactBook extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'name',
        'designation',
        'department',
        'phone',
        'email',
        'address',
        'description',
        'type',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    protected $appends = ['image', 'images'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the primary photo for the contact
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'model')->where('is_primary', true);
    }

    /**
     * Get all media for the contact
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Get tags associated with this contact
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'contact_book_tag');
    }

    /**
     * Get contact numbers for this contact
     */
    public function contactNumbers()
    {
        return $this->hasMany(ContactNumber::class);
    }

    /**
     * Get the primary image URL
     */
    public function getImageAttribute()
    {
        $primary = $this->media()->where('is_primary', true)->first() ?: $this->media()->first();
        if (!$primary) {
            return null;
        }

        return $primary->url;
    }

    /**
     * Get all image URLs with metadata
     */
    public function getImagesAttribute()
    {
        return $this->media->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->url,
                'thumbnail_url' => $media->thumbnail_url,
                'medium_url' => $media->medium_url,
                'is_primary' => $media->is_primary,
                'file_name' => $media->file_name,
                'file_size' => $media->human_file_size,
                'mime_type' => $media->mime_type,
            ];
        });
    }

    /**
     * Scope to filter by tag
     */
    public function scopeByTag(Builder $query, $tagId)
    {
        if (is_array($tagId)) {
            return $query->whereHas('tags', function ($q) use ($tagId) {
                $q->whereIn('tags.id', $tagId);
            });
        }

        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('tags.id', $tagId);
        });
    }

    /**
     * Scope to filter by tag slug
     */
    public function scopeByTagSlug(Builder $query, $tagSlug)
    {
        if (is_array($tagSlug)) {
            return $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->whereIn('tags.slug', $tagSlug);
            });
        }

        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('tags.slug', $tagSlug);
        });
    }

    /**
     * Scope to search across multiple fields
     */
    public function scopeSearch(Builder $query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('designation', 'LIKE', "%{$searchTerm}%")
              ->orWhere('department', 'LIKE', "%{$searchTerm}%")
              ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
              ->orWhere('email', 'LIKE', "%{$searchTerm}%")
              ->orWhere('address', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%")
              ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                  $tagQuery->where('name', 'LIKE', "%{$searchTerm}%");
              });
        });
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType(Builder $query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get only active contacts
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order and name
     */
    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneAttribute()
    {
        // Basic phone formatting - can be enhanced based on requirements
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6)
            );
        }
        
        return $this->phone;
    }

    /**
     * Get full address formatted for display
     */
    public function getFullAddressAttribute()
    {
        return $this->address;
    }

    /**
     * Get contact's primary tag (first tag)
     */
    public function getPrimaryTagAttribute()
    {
        return $this->tags()->first();
    }

    /**
     * Check if contact has a specific tag
     */
    public function hasTag($tagId)
    {
        return $this->tags()->where('tags.id', $tagId)->exists();
    }

    /**
     * Attach tags to contact
     */
    public function attachTags($tagIds)
    {
        if (is_array($tagIds)) {
            $this->tags()->syncWithoutDetaching($tagIds);
        } else {
            $this->tags()->syncWithoutDetaching([$tagIds]);
        }
    }

    /**
     * Sync tags (replace all existing tags)
     */
    public function syncTags($tagIds)
    {
        $this->tags()->sync($tagIds ?? []);
    }
}
