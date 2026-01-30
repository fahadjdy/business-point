<?php

namespace App\Repositories;

use App\Models\ShopProductCategory;

class ShopProductCategoryRepository extends BaseRepository
{
    public function __construct(ShopProductCategory $category)
    {
        parent::__construct($category);
    }

    /**
     * Get categories for a specific shop.
     */
    public function getByShopId(int $shopId)
    {
        return $this->model->where('shop_id', $shopId)
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
