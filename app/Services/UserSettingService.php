<?php

namespace App\Services;

use App\Repositories\UserSettingRepository;

class UserSettingService extends BaseService
{
    /**
     * @var UserSettingRepository
     */
    protected $repository;

    public function __construct(UserSettingRepository $repository)
    {
        parent::__construct($repository);
    }

    public function getSettings(int $userId)
    {
        return $this->repository->getAllForUser($userId)->pluck('value', 'key');
    }

    public function updateSettings(int $userId, array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->repository->updateOrCreate($userId, $key, $value);
        }

        return $this->getSettings($userId);
    }
}
