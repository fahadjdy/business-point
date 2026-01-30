<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseService
{
    protected $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records.
     */
    public function getAll(array $relations = [])
    {
        return $this->repository->all(['*'], $relations);
    }

    /**
     * Get paginated records.
     */
    public function getPaginated(int $perPage = 15, array $params = [], array $relations = [])
    {
        return $this->repository->paginate($perPage, $params, $relations);
    }

    /**
     * Find a record by ID.
     */
    public function findById(int $id, array $relations = [])
    {
        $record = $this->repository->find($id, $relations);
        if (!$record) {
            throw new Exception("Record not found.", 404);
        }
        return $record;
    }

    /**
     * Create a new record within a transaction.
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    /**
     * Update an existing record within a transaction.
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->repository->update($id, $data);
            if (!$record) {
                throw new Exception("Record not found or update failed.", 404);
            }
            return $record;
        });
    }

    /**
     * Delete a record within a transaction.
     */
    public function delete(int $id, ?string $reason = null)
    {
        return DB::transaction(function () use ($id, $reason) {
            $deleted = $this->repository->delete($id, $reason);
            if (!$deleted) {
                throw new Exception("Record not found or deletion failed.", 404);
            }
            return $deleted;
        });
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->restore($id);
        });
    }
}
