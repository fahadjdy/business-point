<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

use App\Services\AdminService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $adminService;
    protected $userService;

    public function __construct(AdminService $adminService, UserService $userService)
    {
        $this->adminService = $adminService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $isAdmin = $request->is('admin/*') || $request->is('admin-data/*'); // Compatibility
        $guard = $isAdmin ? 'admin' : 'sanctum';
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $relations = ['photo'];
        if (!$isAdmin) {
            $relations[] = 'skills';
        }

        return response()->json([
            'success' => true,
            'data' => $user->load($relations)
        ]);
    }

    public function update(Request $request)
    {
        $isAdmin = $request->is('admin/*');
        $guard = $isAdmin ? 'admin' : 'sanctum';
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $table = $isAdmin ? 'admins' : 'users';

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . $table . ',email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'photo' => 'nullable|image|max:2048',
        ];

        if (!$isAdmin) {
            $rules['blood_group'] = 'nullable|string|max:10';
            $rules['gender'] = 'nullable|string|in:male,female,other';
            $rules['skills'] = 'nullable|array';
            $rules['skills.*'] = 'exists:tags,id';
        }

        $request->validate($rules);

        if ($isAdmin) {
            /** @var \App\Models\Admin $user */
            $result = $this->adminService->updateProfile($user, $request->all());
        } else {
            /** @var \App\Models\User $user */
            $result = $this->userService->updateProfile($user, $request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $result
        ]);
    }
}
