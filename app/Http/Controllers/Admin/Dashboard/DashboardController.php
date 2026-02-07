<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVendors = \App\Models\Vendor::count();
        $pendingApprovals = \App\Models\Vendor::where('verification_status', 'pending')->count();
        $totalServices = \App\Models\Shop::count() + \App\Models\Doctor::count() + \App\Models\Barber::count();
        $recentLogs = \App\Models\AuditLog::with('user')->latest()->take(5)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'actor_name' => $log->user ? $log->user->name : 'System',
                'module' => $log->module,
                'action' => $log->action,
                'time' => $log->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    ['label' => 'Total Vendors', 'value' => $totalVendors, 'icon' => 'fa-solid fa-store', 'color' => '#3699ff'],
                    ['label' => 'Active Services', 'value' => $totalServices, 'icon' => 'fa-solid fa-cubes', 'color' => '#1bc5bd'],
                    ['label' => 'Pending Approvals', 'value' => $pendingApprovals, 'icon' => 'fa-solid fa-clock', 'color' => '#ffa800'],
                    ['label' => 'Audit Alerts', 'value' => '0', 'icon' => 'fa-solid fa-triangle-exclamation', 'color' => '#f64e60'], // Placeholder for now
                ],
                'recent_logs' => $recentLogs
            ]
        ]);
    }
}
