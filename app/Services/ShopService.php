<?php

namespace App\Services;

use App\Repositories\ShopRepository;

class ShopService extends BaseService
{
    public function __construct(ShopRepository $shopRepository)
    {
        parent::__construct($shopRepository);
    }
}
