<?php

namespace App\Repositories;

use App\Models\Doctor;

class DoctorRepository extends BaseRepository
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
    }
}
