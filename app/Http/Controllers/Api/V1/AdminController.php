<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    /**
     * List all admins.
     */
    public function index(Request $request)
    {
        return $this->success(
            $this->service->getPaginated($request->query('per_page', 15), $request->all(), ['user']),
            'Admins retrieved'
        );
    }

    /**
     * Create a new admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'is_super_admin' => 'boolean',
        ]);

        return $this->success(
            $this->service->createAdmin($validated, $validated['is_super_admin'] ?? false),
            'Admin created successfully',
            201
        );
    }
}
