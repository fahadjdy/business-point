<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\DoctorService;
use Illuminate\Http\Request;

class DoctorController extends BaseController
{
    protected $service;

    public function __construct(DoctorService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'clinic_name' => 'required|string|max:150',
            'specialization' => 'required|string|max:100',
            'qualification' => 'required|string|max:150',
            'clinic_address' => 'required|string',
            'experience_years' => 'integer|min:0',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Doctor profile created successfully',
            201
        );
    }
}
