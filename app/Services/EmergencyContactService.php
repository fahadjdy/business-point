<?php

namespace App\Services;

use App\Repositories\EmergencyContactRepository;

class EmergencyContactService extends BaseService
{
    public function __construct(EmergencyContactRepository $repository)
    {
        parent::__construct($repository);
    }
}
