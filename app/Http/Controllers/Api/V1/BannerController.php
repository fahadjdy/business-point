<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\BannerService;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    protected $service;

    public function __construct(BannerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->success(
            $this->service->getPaginated(10, ['is_active' => true]),
            'Banners retrieved'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'image_url' => 'nullable|string',
            'link' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Banner created',
            201
        );
    }
}
