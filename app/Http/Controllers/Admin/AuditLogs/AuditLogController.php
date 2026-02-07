<?php

namespace App\Http\Controllers\Admin\AuditLogs;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    protected $service;

    public function __construct(AuditLogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginated($request->query('per_page', 15), $request->all())
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findById($id)
        ]);
    }
}
