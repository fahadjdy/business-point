<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ShopProductCategoryService;
use Illuminate\Http\Request;

class ShopProductCategoryController extends BaseController
{
    protected $service;

    public function __construct(ShopProductCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $shopId = $request->query('shop_id');
        if (!$shopId) {
            return $this->error('shop_id is required');
        }

        return $this->success(
            $this->service->getPaginated(100, ['shop_id' => $shopId]),
            'Categories retrieved'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:100',
            'sort_order' => 'integer',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Category created successfully',
            201
        );
    }
}
