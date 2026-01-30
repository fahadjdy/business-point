<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function login(LoginRequest $request, AuthService $service)
    {
        return $this->success(
            $service->login($request->validated()),
            'Login successful'
        );
    }

    public function register(RegisterRequest $request, AuthService $service)
    {
        return $this->success(
            $service->register($request->validated()),
            'Registration successful',
            201
        );
    }

    public function logout(AuthService $service)
    {
        $service->logout(auth()->user());

        return $this->success(null, 'Logged out successfully');
    }
}
