<?php

namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService extends BaseService
{
    protected $mediaService;

    public function __construct(\App\Repositories\NotificationRepository $repository, \App\Services\MediaService $mediaService)
    {
        parent::__construct($repository);
        $this->mediaService = $mediaService;
    }

    public function create(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $images = $data['images'] ?? [];
            unset($data['images']);

            $notification = $this->repository->create($data);

            if (!empty($images)) {
                $this->uploadMultipleImages($notification, $images);
            }

            return $notification->load('media');
        });
    }

    public function update(int $id, array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $images = $data['images'] ?? [];
            $deleteImages = $data['delete_images'] ?? [];
            unset($data['images'], $data['delete_images']);

            $notification = $this->repository->update($id, $data);

            // Delete specified images
            if (!empty($deleteImages)) {
                $this->mediaService->deleteMediaByIds($deleteImages);
            }

            // Upload new images
            if (!empty($images)) {
                $this->uploadMultipleImages($notification, $images);
            }

            return $notification->load('media');
        });
    }

    private function uploadMultipleImages($notification, array $images)
    {
        foreach ($images as $image) {
            $this->mediaService->upload($image, $notification, 'notifications', false, true);
        }
    }
}
