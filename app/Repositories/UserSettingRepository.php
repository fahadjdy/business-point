<?php

namespace App\Repositories;

use App\Models\UserSetting;

class UserSettingRepository extends BaseRepository
{
    public function __construct(UserSetting $model)
    {
        parent::__construct($model);
    }

    public function findByKey(int $userId, string $key)
    {
        return $this->model->where('user_id', $userId)
            ->where('key', $key)
            ->first();
    }

    public function getAllForUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function updateOrCreate(int $userId, string $key, $value)
    {
        return $this->model->updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );
    }
}
