<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends BaseController
{
    protected $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }

    /**
     * Create or update a shop profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'shop_name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'price_display_enabled' => 'boolean',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Shop profile created/updated successfully',
            201
        );
    }

    public function show(int $id)
    {
        return $this->success(
            $this->service->findById($id),
            'Shop retrieved'
        );
    }
}
