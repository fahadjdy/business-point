<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasGlobalSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, HasGlobalSoftDeletes;

    protected $fillable = [
        'model_type',
        'model_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'is_primary',
        'uploaded_by',
        'deleted_by',
        'delete_reason'
    ];

    protected $appends = ['url', 'thumbnail_url', 'medium_url', 'human_file_size'];

    public function model()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full URL for the original image
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        
        // Remove leading 'storage/' if it exists to avoid duplication
        $cleanPath = ltrim($this->file_path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8); // Remove 'storage/' prefix
        }
        
        return asset('storage/' . $cleanPath);
    }

    /**
     * Get the thumbnail URL (150x150)
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->isImage()) {
            return $this->getUrlAttribute();
        }

        $thumbnailPath = $this->getResizedPath('thumbnail');
        
        // Remove leading 'storage/' if it exists
        $cleanThumbnailPath = ltrim($thumbnailPath, '/');
        if (str_starts_with($cleanThumbnailPath, 'storage/')) {
            $cleanThumbnailPath = substr($cleanThumbnailPath, 8);
        }
        
        if (Storage::disk('public')->exists($cleanThumbnailPath)) {
            return asset('storage/' . $cleanThumbnailPath);
        }
        
        // Fallback to original if thumbnail doesn't exist
        return $this->getUrlAttribute();
    }

    /**
     * Get the medium URL (400x400)
     */
    public function getMediumUrlAttribute(): string
    {
        if (!$this->isImage()) {
            return $this->getUrlAttribute();
        }

        $mediumPath = $this->getResizedPath('medium');
        
        // Remove leading 'storage/' if it exists
        $cleanMediumPath = ltrim($mediumPath, '/');
        if (str_starts_with($cleanMediumPath, 'storage/')) {
            $cleanMediumPath = substr($cleanMediumPath, 8);
        }
        
        if (Storage::disk('public')->exists($cleanMediumPath)) {
            return asset('storage/' . $cleanMediumPath);
        }
        
        // Fallback to original if medium doesn't exist
        return $this->getUrlAttribute();
    }

    /**
     * Check if the media is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get the path for resized images
     */
    public function getResizedPath(string $size): string
    {
        $pathInfo = pathinfo($this->file_path);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        
        return "{$directory}/{$size}_{$filename}.{$extension}";
    }

    /**
     * Get file size in human readable format
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
