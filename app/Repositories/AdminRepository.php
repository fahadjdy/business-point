<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository extends BaseRepository
{
    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    /**
     * Find admin by user ID.
     */
    public function findByUserId(int $userId): ?Admin
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
