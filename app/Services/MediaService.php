<?php

namespace App\Services;

use App\Models\Media;
use App\Repositories\MediaRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class MediaService extends BaseService
{
    public function __construct(MediaRepository $repository)
    {
        parent::__construct($repository);
    }

    public function upload(UploadedFile $file, $model, $arg3 = 'default', bool $arg4 = false, bool $arg5 = false): Media
    {
        if ($model instanceof Model) {
            $modelType = get_class($model);
            $modelId = $model->id;
            $collection = is_string($arg3) ? $arg3 : 'default';
            $isPrimary = $arg4;
            $withResize = $arg5;
        } else {
            $modelType = (string)$model;
            $modelId = (int)$arg3;
            $collection = 'default';
            $isPrimary = $arg4;
            $withResize = $arg5;
        }
        
        // Custom Filename: date-month-year-timestamp-filename
        $timestamp = now()->timestamp;
        $dateStr = now()->format('d-M-Y'); // e.g., 28-Jan-2025
        $originalName = str_replace(' ', '-', $file->getClientOriginalName());
        $fileName = strtolower("{$dateStr}-{$timestamp}-{$originalName}");

        // Directory Structure: storage/{collection}/{model_id}/{filename}
        $targetDir = $this->getStorageDirectory($collection, $modelId);

        $path = $file->storeAs($targetDir, $fileName, 'public');

        $media = $this->repository->create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'file_path' => $path,
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id() ?? null,
            'is_primary' => $isPrimary
        ]);

        // Create resized versions if requested and it's an image
        if ($withResize && $media->isImage()) {
            $this->createResizedVersions($media, $file);
        }

        return $media;
    }

    /**
     * Upload a file with automatic image resizing
     */
    public function uploadWithResize(UploadedFile $file, $model, $arg3 = null, bool $isPrimary = false): Media
    {
        if ($model instanceof Model) {
            return $this->upload($file, $model, 'default', $isPrimary, true);
        }
        
        return $this->upload($file, $model, $arg3, $isPrimary, true);
    }


    /**
     * Create resized versions of an image
     */
    protected function createResizedVersions(Media $media, UploadedFile $file): void
    {
        // Check if GD extension is loaded
        if (!\extension_loaded('gd')) {
            \Log::warning('GD extension NOT loaded. Skipping image resizing.');
            return;
        }

        $sizes = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 400, 'height' => 400],
        ];

        foreach ($sizes as $sizeName => $dimensions) {
            $this->resizeImage($media, $file->getPathname(), $sizeName, $dimensions['width'], $dimensions['height']);
        }
    }

    /**
     * Resize an image using GD library
     */
    protected function resizeImage(Media $media, string $sourceFilePath, string $sizeName, int $width, int $height): void
    {
        // Get image info
        $imageInfo = \getimagesize($sourceFilePath);
        if (!$imageInfo) {
            return; // Not a valid image
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];

        // Create image resource from file
        $sourceImage = $this->createImageFromFile($sourceFilePath, $imageType);
        if (!$sourceImage) {
            return;
        }

        // Calculate new dimensions maintaining aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;
        
        if ($width / $height > $aspectRatio) {
            $newWidth = $height * $aspectRatio;
            $newHeight = $height;
        } else {
            $newWidth = $width;
            $newHeight = $width / $aspectRatio;
        }

        // Create new image
        $resizedImage = \imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            \imagealphablending($resizedImage, false);
            \imagesavealpha($resizedImage, true);
            $transparent = \imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            \imagefill($resizedImage, 0, 0, $transparent);
        }

        // Resize the image
        \imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // Save resized image
        $resizedPath = $media->getResizedPath($sizeName);
        $fullPath = storage_path('app/public/' . $resizedPath);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->saveImageToFile($resizedImage, $fullPath, $imageType);

        // Clean up memory
        \imagedestroy($sourceImage);
        \imagedestroy($resizedImage);
    }

    /**
     * Create image resource from file
     */
    protected function createImageFromFile(string $filePath, int $imageType)
    {
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                return \imagecreatefromjpeg($filePath);
            case IMAGETYPE_PNG:
                return \imagecreatefrompng($filePath);
            case IMAGETYPE_GIF:
                return \imagecreatefromgif($filePath);
            case IMAGETYPE_WEBP:
                return \imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }

    /**
     * Save image resource to file
     */
    protected function saveImageToFile($imageResource, string $filePath, int $imageType): void
    {
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                \imagejpeg($imageResource, $filePath, 85);
                break;
            case IMAGETYPE_PNG:
                \imagepng($imageResource, $filePath, 8);
                break;
            case IMAGETYPE_GIF:
                \imagegif($imageResource, $filePath);
                break;
            case IMAGETYPE_WEBP:
                \imagewebp($imageResource, $filePath, 85);
                break;
        }
    }

    /**
     * Get storage directory for a model
     */
    protected function getStorageDirectory(string $collection, int $modelId): string
    {
        return "{$collection}/{$modelId}";
    }

    /**
     * Get all media for a model
     */
    public function getModelMedia(string $modelType, int $modelId)
    {
        return $this->repository->getForModel($modelType, $modelId);
    }

    /**
     * Delete multiple media entries by their IDs.
     */
    public function deleteMediaByIds(array $mediaIds): void
    {
        foreach ($mediaIds as $mediaId) {
            $this->deleteMedia($mediaId);
        }
    }

    /**
     * Delete all media associated with a model.
     */
    public function deleteModelMedia($model): void
    {
        $modelType = get_class($model);
        $modelId = $model->id;
        
        $mediaEntries = $this->repository->getForModel($modelType, $modelId);

        foreach ($mediaEntries as $media) {
            $this->deleteMedia($media);
        }
    }

    /**
     * Delete a specific media entry and its files.
     */
    public function deleteMedia($mediaId): void
    {
        $media = is_numeric($mediaId) ? $this->repository->find($mediaId) : $mediaId;
        
        if (!$media) {
            return;
        }

        // Delete original file
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Delete resized versions if they exist
        if ($media->isImage()) {
            $sizes = ['thumbnail', 'medium'];
            foreach ($sizes as $size) {
                $resizedPath = $media->getResizedPath($size);
                if (Storage::disk('public')->exists($resizedPath)) {
                    Storage::disk('public')->delete($resizedPath);
                }
            }
        }

        $media->delete();
    }
}
