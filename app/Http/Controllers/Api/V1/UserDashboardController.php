<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        return $this->success([
            'stats' => [
                ['label' => 'Total Bookings', 'value' => '0', 'icon' => 'fa-solid fa-calendar-check', 'color' => '#3699ff'],
                ['label' => 'Saved Vendors', 'value' => '0', 'icon' => 'fa-solid fa-heart', 'color' => '#f64e60'],
                ['label' => 'Messages', 'value' => '0', 'icon' => 'fa-solid fa-comment-dots', 'color' => '#ffa800'],
                ['label' => 'Notifications', 'value' => '0', 'icon' => 'fa-solid fa-bell', 'color' => '#1bc5bd'],
            ],
            'recent_activities' => []
        ], 'User dashboard data retrieved successfully');
    }
}
