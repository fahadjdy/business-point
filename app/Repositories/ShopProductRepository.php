<?php

namespace App\Repositories;

use App\Models\ShopProduct;

class ShopProductRepository extends BaseRepository
{
    public function __construct(ShopProduct $product)
    {
        parent::__construct($product);
    }

    /**
     * Get products by category.
     */
    public function getByCategoryId(int $categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get all products for a shop.
     */
    public function getByShopId(int $shopId)
    {
        return $this->model->where('shop_id', $shopId)->get();
    }
}
