<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'title',
        'message',
        'priority',
        'is_active',
        'sort_order',
        'is_scheduled',
        'scheduled_at',
        'deleted_by',
        'delete_reason'
    ];

    public function photo()
    {
        return $this->morphOne(Media::class, 'model');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
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

    protected $appends = ['image', 'images'];
}
