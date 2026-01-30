<?php

namespace App\Services;

use App\Repositories\DoctorRepository;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $doctorRepository)
    {
        parent::__construct($doctorRepository);
    }
}
