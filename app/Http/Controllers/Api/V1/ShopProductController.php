<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ShopProductService;
use Illuminate\Http\Request;

class ShopProductController extends BaseController
{
    protected $service;

    public function __construct(ShopProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $shopId = $request->query('shop_id');
        $categoryId = $request->query('category_id');

        $filters = [];
        if ($shopId)
            $filters['shop_id'] = $shopId;
        if ($categoryId)
            $filters['category_id'] = $categoryId;

        return $this->success(
            $this->service->getPaginated($request->query('per_page', 15), $filters),
            'Products retrieved'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'category_id' => 'required|exists:shop_product_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        return $this->success(
            $this->service->create($validated),
            'Product created successfully',
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:shop_product_categories,id',
            'name' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        return $this->success(
            $this->service->update($id, $validated),
            'Product updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return $this->success(null, 'Product deleted successfully');
    }
}
