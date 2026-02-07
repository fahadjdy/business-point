<?php

namespace App\Http\Controllers\Admin\Banners;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    protected $service;

    public function __construct(BannerService $service)
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

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findById($id)
        ]);
    }

    public function store(StoreBannerRequest $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully',
            'data' => $this->service->create($request->validated())
        ]);
    }

    public function update(UpdateBannerRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully',
            'data' => $this->service->update($id, $request->validated())
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }
}
