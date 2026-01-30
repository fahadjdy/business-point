<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class TagRepository extends BaseRepository
{
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    /**
     * Get paginated tags with filtering
     */
    public function getPaginatedWithFilters(int $perPage = 50, array $params = [])
    {
        $query = $this->model->query()->active();

        // Apply filters
        if (!empty($params['category'])) {
            $query->byCategory($params['category']);
        }

        if (!empty($params['search'])) {
            $query->where('name', 'LIKE', "%{$params['search']}%");
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * Get all active tags without pagination
     */
    public function getAllWithFilters(array $params = [])
    {
        $query = $this->model->query()->active();

        // Apply filters
        if (!empty($params['category'])) {
            $query->byCategory($params['category']);
        }

        if (!empty($params['search'])) {
            $query->where('name', 'LIKE', "%{$params['search']}%");
        }

        return $query->ordered()->get();
    }

    /**
     * Get tags grouped by category
     */
    public function getGroupedByCategory(array $params = [])
    {
        $query = $this->model->query()->active();

        if (!empty($params['search'])) {
            $query->where('name', 'LIKE', "%{$params['search']}%");
        }

        $tags = $query->ordered()->get();

        return $tags->groupBy('category')->map(function ($categoryTags) {
            return $categoryTags->values();
        });
    }

    /**
     * Count active tags
     */
    public function countActive()
    {
        return $this->model->active()->count();
    }

    /**
     * Get count by category
     */
    public function getCountByCategory()
    {
        return $this->model->active()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get most used tags (based on contact associations)
     */
    public function getMostUsedTags(int $limit = 5)
    {
        return $this->model->active()
            ->withCount('contacts')
            ->orderByDesc('contacts_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular tags with contact count
     */
    public function getPopularTags(int $limit = 10)
    {
        return $this->model->active()
            ->withCount(['contacts' => function ($query) {
                $query->where('is_active', true);
            }])
            ->having('contacts_count', '>', 0)
            ->orderByDesc('contacts_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Search tags by name
     */
    public function searchByName(string $query, ?string $category = null)
    {
        $builder = $this->model->active()
            ->where('name', 'LIKE', "%{$query}%");

        if ($category) {
            $builder->byCategory($category);
        }

        return $builder->ordered()->get();
    }

    /**
     * Get tags by IDs
     */
    public function getByIds(array $ids)
    {
        return $this->model->active()->whereIn('id', $ids)->get();
    }

    /**
     * Get tags by category
     */
    public function getByCategory(string $category)
    {
        return $this->model->active()->byCategory($category)->ordered()->get();
    }
}