<?php

namespace App\Services;

use App\Repositories\BarberRepository;

class BarberService extends BaseService
{
    public function __construct(BarberRepository $barberRepository)
    {
        parent::__construct($barberRepository);
    }
}
