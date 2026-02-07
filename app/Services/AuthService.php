<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @property \App\Repositories\UserRepository $repository
 */
class AuthService extends BaseService
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }

    /**
     * Authenticate a user and return token.
     */
    public function login(array $credentials)
    {
        $user = $this->repository->findByLogin($credentials['login']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid credentials.'],
            ]);
        }

        if (!$user->is_active) {
            throw new Exception("Your account is inactive. Please contact support.", 403);
        }

        $tokenName = $credentials['device_name'] ?? 'auth_token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return [
            'user' => $user->load('vendor'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new user.
     */
    public function register(array $data)
    {
        // Map mobile to phone if provided
        if (isset($data['mobile'])) {
            $data['phone'] = $data['mobile'];
            unset($data['mobile']);
        }
        
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true; // Auto-activate new users
        
        return $this->create($data);
    }

    /**
     * Logout user by revoking current token.
     */
    public function logout($user)
    {
        return $user->currentAccessToken()->delete();
    }
}
