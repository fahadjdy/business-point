<?php

namespace App\Repositories;

use App\Models\Barber;

class BarberRepository extends BaseRepository
{
    public function __construct(Barber $barber)
    {
        parent::__construct($barber);
    }
}
