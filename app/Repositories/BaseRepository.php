<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records.
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * Paginate records with search, sort, and filter.
     */
    public function paginate(
        int $perPage = 15,
        array $params = [],
        array $relations = [],
        array $columns = ['*']
    ): LengthAwarePaginator {
        $query = $this->model->with($relations);

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        return $query->paginate($perPage, $columns);
    }

    /**
     * Apply filters to the query.
     */
    protected function applyFilters($query, array $params)
    {
        // Support both nested 'filters' array and flat params
        $filters = $params['filters'] ?? collect($params)->except(['search', 'sort_by', 'sort_order', 'page', 'per_page'])->toArray();

        // Apply filters
        if (is_array($filters)) {
            foreach ($filters as $field => $value) {
                if ($value === null || $value === '')
                    continue;

                if (str_contains($field, '.')) {
                    $parts = explode('.', $field);
                    $query->whereHas($parts[0], function ($q) use ($parts, $value) {
                        $q->where($parts[1], $value);
                    });
                } elseif (str_ends_with($field, '_min')) {
                    $query->where(str_replace('_min', '', $field), '>=', $value);
                } elseif (str_ends_with($field, '_max')) {
                    $query->where(str_replace('_max', '', $field), '<=', $value);
                } elseif (str_ends_with($field, '_date')) {
                    $query->whereDate(str_replace('_date', '', $field), $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        // Apply search
        if (isset($params['search']) && !empty($params['search'])) {
            $search = $params['search'];
            $searchableFields = $this->getSearchableFields();

            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Apply sorting to the query.
     */
    protected function applySorting($query, array $params)
    {
        $sortField = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        if (str_contains($sortField, '.')) {
            $parts = explode('.', $sortField);
            // Basic relation sorting
            $query->orderBy(
                $this->model->join($parts[0], $this->model->getTable() . '.' . $parts[0] . '_id', '=', $parts[0] . '.id')
                    ->select($parts[0] . '.' . $parts[1])
                    ->limit(1),
                $sortOrder
            );
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query;
    }

    /**
     * Find a record by ID.
     */
    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record.
     */
    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    /**
     * Delete a record (soft delete supported via trait).
     */
    public function delete(int $id, ?string $reason = null): bool
    {
        $record = $this->find($id);
        if ($record) {
            if ($reason) {
                $record->delete_reason = $reason;
                $record->save();
            }
            return $record->delete();
        }
        return false;
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(int $id): bool
    {
        $record = $this->model->onlyTrashed()->find($id);
        if ($record) {
            return $record->restore();
        }
        return false;
    }

    /**
     * Get searchable fields for the model.
     */
    protected function getSearchableFields(): array
    {
        return $this->model->getFillable();
    }
}
