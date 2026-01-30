<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends BaseController
{
    protected $service;

    public function __construct(MediaService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'is_primary' => 'boolean',
        ]);

        return $this->success(
            $this->service->upload(
                $request->file('file'),
                $request->input('model_type'),
                $request->input('model_id'),
                $request->boolean('is_primary')
            ),
            'Media uploaded successfully',
            201
        );
    }

    /**
     * Store multiple files for announcements
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:10240', // 10MB max per image
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $uploadedMedia = [];
        $files = $request->file('files');

        foreach ($files as $index => $file) {
            $isPrimary = $index === 0; // First image is primary
            
            $media = $this->service->uploadWithResize(
                $file,
                $request->input('model_type'),
                $request->input('model_id'),
                $isPrimary
            );
            
            $uploadedMedia[] = $media;
        }

        return $this->success(
            $uploadedMedia,
            'Multiple media files uploaded successfully',
            201
        );
    }

    public function destroy(int $id)
    {
        $this->service->deleteMedia($id);
        return $this->success(null, 'Media deleted');
    }

    /**
     * Get media for a specific model
     */
    public function getModelMedia(Request $request)
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $media = $this->service->getModelMedia(
            $request->input('model_type'),
            $request->input('model_id')
        );

        return $this->success(
            $media,
            'Media retrieved successfully'
        );
    }
}
