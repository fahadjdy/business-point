<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
    protected $service;

    public function __construct(VendorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginated($request->query('per_page', 10), $request->all())
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getVendorProfile($id),
        ]);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id, request('reason'));
        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully',
        ]);
    }
}
