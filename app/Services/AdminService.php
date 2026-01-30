<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService extends BaseService
{
    protected $userRepository;
    protected $mediaService;

    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository, MediaService $mediaService)
    {
        parent::__construct($adminRepository);
        $this->userRepository = $userRepository;
        $this->mediaService = $mediaService;
    }

    /**
     * Create a new admin user.
     */
    public function createAdmin(array $userData, bool $isSuperAdmin = false)
    {
        return DB::transaction(function () use ($userData, $isSuperAdmin) {
            $userData['password'] = Hash::make($userData['password']);
            $user = $this->userRepository->create($userData);

            return $this->repository->create([
                'user_id' => $user->id,
                'is_super_admin' => $isSuperAdmin,
            ]);
        });
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Admin $admin, array $data)
    {
        return DB::transaction(function () use ($admin, $data) {
            $updateData = [
                'name' => $data['name'] ?? $admin->name,
                'email' => $data['email'] ?? $admin->email,
                'phone' => $data['phone'] ?? $admin->phone,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $admin->update($updateData);

            // Handle photo
            if (isset($data['photo'])) {
                $this->mediaService->upload($data['photo'], $admin, 'uploads/profile', true, true);
            }

            return $admin->load(['photo']);
        });
    }
}
