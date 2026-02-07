<?php

namespace App\Services;

use App\Repositories\BannerRepository;

class BannerService extends BaseService
{
    protected $mediaService;

    public function __construct(BannerRepository $repository, \App\Services\MediaService $mediaService)
    {
        parent::__construct($repository);
        $this->mediaService = $mediaService;
    }

    public function create(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $image = $data['image'] ?? null;
            unset($data['image']);

            $banner = $this->repository->create($data);

            if ($image) {
                $this->mediaService->upload($image, $banner, 'banners', true, true);
            }

            return $banner->load('media');
        });
    }

    public function update(int $id, array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $image = $data['image'] ?? null;
            unset($data['image']);

            $banner = $this->repository->update($id, $data);

            if ($image) {
                // Delete old image first
                $banner->media()->delete();
                $this->mediaService->upload($image, $banner, 'banners', true, true);
            }

            return $banner->load('media');
        });
    }
}
