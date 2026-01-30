<?php

namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends BaseRepository
{
    public function __construct(Banner $banner)
    {
        parent::__construct($banner);
    }

    /**
     * Get active banners.
     */
    public function getActive()
    {
        return $this->model->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();
    }
}
