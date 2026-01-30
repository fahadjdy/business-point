<?php

namespace App\Services;

use App\Repositories\ShopProductRepository;

class ShopProductService extends BaseService
{
    public function __construct(ShopProductRepository $repository)
    {
        parent::__construct($repository);
    }
}
