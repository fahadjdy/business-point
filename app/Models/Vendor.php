<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'user_id',
        'vendor_type',
        'business_name',
        'description',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'is_verified',
        'is_public',
        'verification_status',
        'status',
        'latitude',
        'longitude',
        'deleted_by',
        'delete_reason'
    ];

    protected $appends = ['image', 'images'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

    public function openingTimes()
    {
        return $this->hasMany(VendorOpeningTime::class);
    }

    /**
     * Get all media for this vendor
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Get primary image URL
     */
    public function getImageAttribute()
    {
        return $this->media()->where('is_primary', true)->first()?->url;
    }

    /**
     * Get all images URLs
     */
    public function getImagesAttribute()
    {
        return $this->media()->get()->map(fn($m) => $m->url);
    }

    /**
     * Get tags associated with this vendor
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'vendor_tags');
    }

    /**
     * Sync tags (replace all existing tags)
     */
    public function syncTags($tagIds)
    {
        $this->tags()->sync($tagIds ?? []);
    }
}
