<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    ['label' => 'Total Vendors', 'value' => '124', 'icon' => 'fa-solid fa-store', 'color' => '#3699ff'],
                    ['label' => 'Active Services', 'value' => '56', 'icon' => 'fa-solid fa-cubes', 'color' => '#1bc5bd'],
                    ['label' => 'New Inquiries', 'value' => '12', 'icon' => 'fa-solid fa-envelope', 'color' => '#ffa800'],
                    ['label' => 'Audit Alerts', 'value' => '3', 'icon' => 'fa-solid fa-triangle-exclamation', 'color' => '#f64e60'],
                ],
                'recent_logs' => [
                    ['id' => 1, 'actor_name' => 'Super Admin', 'module' => 'Vendor', 'action' => 'Created', 'time' => '5 mins ago'],
                    ['id' => 2, 'actor_name' => 'Super Admin', 'module' => 'Settings', 'action' => 'Updated', 'time' => '1 hour ago'],
                    ['id' => 3, 'actor_name' => 'Vendor User', 'module' => 'Product', 'action' => 'Created', 'time' => '2 hours ago'],
                ]
            ]
        ]);
    }
}
