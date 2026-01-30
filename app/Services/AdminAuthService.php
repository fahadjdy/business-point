<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminAuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials)
    {
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (!$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact support.'],
            ]);
        }

        Auth::guard('admin')->login($admin, $credentials['remember'] ?? false);

        return [
            'user' => $admin,
            'message' => 'Logged in successfully'
        ];
    }

    /**
     * Register a new admin user.
     */
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
                'is_super_admin' => false,
            ]);

            Auth::guard('admin')->login($admin);

            return [
                'user' => $admin,
                'message' => 'Registered successfully'
            ];
        });
    }

    /**
     * Logout user by clearing session.
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return true;
    }
}
