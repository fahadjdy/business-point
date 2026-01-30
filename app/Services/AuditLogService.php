<?php

namespace App\Services;

use App\Repositories\AuditLogRepository;

class AuditLogService extends BaseService
{
    public function __construct(AuditLogRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Log a global audit action.
     */
    public function log(array $data)
    {
        return $this->repository->create($data);
    }
}
