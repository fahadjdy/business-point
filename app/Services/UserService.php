<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    protected $mediaService;

    public function __construct(UserRepository $repository, MediaService $mediaService)
    {
        parent::__construct($repository);
        $this->mediaService = $mediaService;
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                'blood_group' => $data['blood_group'] ?? $user->blood_group,
                'gender' => $data['gender'] ?? $user->gender,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Handle skills (tags)
            if (isset($data['skills'])) {
                $user->skills()->sync($data['skills']);
            }

            // Handle photo
            if (isset($data['photo'])) {
                $this->mediaService->upload($data['photo'], $user, 'uploads/profile', true, true);
            }

            return $user->load(['photo', 'skills']);
        });
    }
}
