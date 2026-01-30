<?php

namespace App\Repositories;

use App\Models\ContactBookAuditLog;

class ContactBookAuditLogRepository extends BaseRepository
{
    public function __construct(ContactBookAuditLog $auditLog)
    {
        parent::__construct($auditLog);
    }

    /**
     * Get paginated audit logs with enhanced filtering
     */
    public function getPaginatedWithFilters(int $perPage = 15, array $params = [])
    {
        $query = $this->model->query()->with(['user', 'contactBook']);

        // Apply filters
        if (!empty($params['contact_id'])) {
            $query->where('contact_book_id', $params['contact_id']);
        }

        if (!empty($params['action'])) {
            $query->where('action', $params['action']);
        }

        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function applyFilters($query, array $params)
    {
        if (isset($params['keyword']) && $params['keyword']) {
            $keyword = $params['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('action', 'LIKE', "%{$keyword}%")
                    ->orWhere('search_keyword', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'LIKE', "%{$keyword}%");
                    })
                    ->orWhereHas('contactBook', function ($cq) use ($keyword) {
                        $cq->where('name', 'LIKE', "%{$keyword}%");
                    });
            });
            unset($params['keyword']);
        }

        return parent::applyFilters($query, $params);
    }
}
