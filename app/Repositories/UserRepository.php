<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Find user by login (email or phone).
     */
    public function findByLogin(string $login): ?User
    {
        return $this->model->where('email', $login)
            ->orWhere('phone', $login)
            ->first();
    }
}
