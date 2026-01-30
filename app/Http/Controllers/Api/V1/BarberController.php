<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\BarberService;
use Illuminate\Http\Request;

class BarberController extends BaseController
{
    protected $service;

    public function __construct(BarberService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'shop_name' => 'required|string|max:150',
            'services' => 'required|string',
            'address' => 'required|string',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Barber profile created successfully',
            201
        );
    }
}
