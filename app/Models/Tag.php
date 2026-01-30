<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasGlobalSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Model
{
    use HasFactory, HasGlobalSoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get vendors associated with this tag
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_tags');
    }

    /**
     * Get contacts associated with this tag
     */
    public function contacts()
    {
        return $this->belongsToMany(ContactBook::class, 'contact_book_tag');
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory(Builder $query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get only active tags
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by name
     */
    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Get the count of contacts using this tag
     */
    public function getContactsCountAttribute()
    {
        return $this->contacts()->count();
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug($name)
    {
        return \Illuminate\Support\Str::slug($name);
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = static::generateSlug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = static::generateSlug($tag->name);
            }
        });
    }
}
