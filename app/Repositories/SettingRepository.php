<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }

    /**
     * Get setting value by key.
     */
    public function getByKey(string $key)
    {
        return $this->model->where('key', $key)->first();
    }
}
