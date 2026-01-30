<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\VendorService;
use Illuminate\Http\Request;

class VendorController extends BaseController
{
    protected $service;

    public function __construct(VendorService $service)
    {
        $this->service = $service;
    }

    /**
     * List vendors with filtering and search.
     */
    public function index(Request $request)
    {
        return $this->success(
            $this->service->getPaginated($request->query('per_page', 15), $request->all()),
            'Vendors retrieved successfully'
        );
    }

    /**
     * Show vendor profile with full details.
     */
    public function show(int $id)
    {
        try {
            return $this->success(
                $this->service->getVendorProfile($id),
                'Vendor profile retrieved'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], $e->getCode() ?: 404);
        }
    }
    /**
     * Store a new vendor (Register).
     */
    public function store(\App\Http\Requests\Vendor\StoreVendorRequest $request)
    {
        return $this->success(
            $this->service->registerVendor($request->validated()),
            'Registration successful! Please wait for admin approval.',
            201
        );
    }
}
