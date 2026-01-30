<?php

namespace App\Services;

use App\Repositories\TagRepository;

/**
 * @property \App\Repositories\TagRepository $repository
 */
class TagService extends BaseService
{
    public function __construct(TagRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get paginated tags with filtering
     */
    public function getPaginatedTags(int $perPage = 50, array $params = [])
    {
        return $this->repository->getPaginatedWithFilters($perPage, $params);
    }

    /**
     * Get all active tags without pagination
     */
    public function getAllTags(array $params = [])
    {
        return $this->repository->getAllWithFilters($params);
    }

    /**
     * Get tags grouped by category
     */
    public function getTagsGroupedByCategory(array $params = [])
    {
        return $this->repository->getGroupedByCategory($params);
    }

    /**
     * Get tag statistics
     */
    public function getTagStats()
    {
        return [
            'total_tags' => $this->repository->count(),
            'active_tags' => $this->repository->countActive(),
            'tags_by_category' => $this->repository->getCountByCategory(),
            'most_used_tags' => $this->repository->getMostUsedTags(5),
        ];
    }

    /**
     * Get popular tags (most used in contacts)
     */
    public function getPopularTags(int $limit = 10)
    {
        return $this->repository->getPopularTags($limit);
    }

    /**
     * Search tags by name
     */
    public function searchTags(string $query, ?string $category = null)
    {
        return $this->repository->searchByName($query, $category);
    }
}