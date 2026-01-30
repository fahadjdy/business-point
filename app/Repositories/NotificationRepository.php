<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }
    
    public function paginate(int $perPage = 15, array $params = [], array $relations = ['media'], array $columns = ['*']): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with($relations);

        // Filter out scheduled notifications if they are for the public
        if (isset($params['for_public']) && $params['for_public']) {
            $query->where(function ($q) {
                $q->where('is_scheduled', false)
                    ->orWhere(function ($sq) {
                        $sq->where('is_scheduled', true)
                            ->where('scheduled_at', '<=', now());
                    });
            });
        }

        // Handle date filtering
        if (isset($params['date_from']) && !empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }
        
        if (isset($params['date_to']) && !empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }

        $appliedParams = collect($params)->except(['for_public', 'date_from', 'date_to'])->toArray();
        $query = $this->applyFilters($query, $appliedParams);
        $query = $this->applySorting($query, $appliedParams);

        return $query->paginate($perPage, $columns);
    }

    public function find(int $id, array $relations = ['media']): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function findOrFail(int $id, array $relations = ['media'])
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Get searchable fields for notifications.
     */
    protected function getSearchableFields(): array
    {
        return ['title', 'message'];
    }
}
