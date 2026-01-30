<?php

namespace App\Repositories;

use App\Models\AuditLog;

class AuditLogRepository extends BaseRepository
{
    public function __construct(AuditLog $auditLog)
    {
        parent::__construct($auditLog);
    }

    /**
     * Get audit logs for a specific module.
     */
    public function getByModule(string $module)
    {
        return $this->model->where('module', $module)
            ->orderBy('id', 'desc')
            ->paginate(15);
    }
}
