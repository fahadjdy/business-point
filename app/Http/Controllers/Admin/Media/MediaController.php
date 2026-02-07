<?php

namespace App\Http\Controllers\Admin\Media;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
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

        return response()->json([
            'success' => true,
            'message' => 'Media uploaded successfully',
            'data' => $this->service->upload(
                $request->file('file'),
                $request->input('model_type'),
                $request->input('model_id'),
                $request->boolean('is_primary')
            )
        ], 201);
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $uploadedMedia = [];
        $files = $request->file('files');

        foreach ($files as $index => $file) {
            $isPrimary = $index === 0;
            
            $media = $this->service->uploadWithResize(
                $file,
                $request->input('model_type'),
                $request->input('model_id'),
                $isPrimary
            );
            
            $uploadedMedia[] = $media;
        }

        return response()->json([
            'success' => true,
            'message' => 'Multiple media files uploaded successfully',
            'data' => $uploadedMedia
        ], 201);
    }

    public function destroy(int $id)
    {
        $this->service->deleteMedia($id);
        return response()->json([
            'success' => true,
            'message' => 'Media deleted'
        ]);
    }

    public function getModelMedia(Request $request)
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => $this->service->getModelMedia(
                $request->input('model_type'),
                $request->input('model_id')
            )
        ]);
    }
}
