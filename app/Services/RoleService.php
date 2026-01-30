<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $roleRepository)
    {
        parent::__construct($roleRepository);
    }
}
