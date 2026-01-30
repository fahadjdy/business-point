<?php

namespace App\Services;

use App\Repositories\ShopProductCategoryRepository;

class ShopProductCategoryService extends BaseService
{
    public function __construct(ShopProductCategoryRepository $repository)
    {
        parent::__construct($repository);
    }
}
