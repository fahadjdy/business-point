<?php

namespace App\Repositories;

use App\Models\ContactBook;
use Illuminate\Database\Eloquent\Builder;

class ContactBookRepository extends BaseRepository
{
    public function __construct(ContactBook $contactBook)
    {
        parent::__construct($contactBook);
    }

    /**
     * Get paginated contacts with enhanced filtering
     */
    public function getPaginatedWithFilters(int $perPage = 20, array $params = [])
    {
        $query = $this->model->query()->with(['tags', 'media']);

        // Apply filters
        if (!empty($params['keyword'])) {
            $query->search($params['keyword']);
        }

        if (!empty($params['type'])) {
            $query->byType($params['type']);
        }

        if (!empty($params['tag_id'])) {
            $query->byTag($params['tag_id']);
        }

        if (!empty($params['tag'])) {
            $query->byTagSlug($params['tag']);
        }

        if (isset($params['is_active'])) {
            if ($params['is_active']) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        } else {
            // Default to active contacts only
            $query->active();
        }

        // Apply sorting
        $sortBy = $params['sort_by'] ?? 'name';
        $sortOrder = $params['sort_order'] ?? 'asc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find contact with specific relations
     */
    public function findWithRelations(int $id, array $relations = [])
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get contacts by tag ID
     */
    public function getByTag(int $tagId, int $perPage = 20)
    {
        return $this->model->byTag($tagId)
            ->active()
            ->with(['tags', 'media'])
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get contacts by tag slug
     */
    public function getByTagSlug(string $tagSlug, int $perPage = 20)
    {
        return $this->model->byTagSlug($tagSlug)
            ->active()
            ->with(['tags', 'media'])
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Search contacts with pagination
     */
    public function searchPaginated(string $query, ?string $type = null, int $perPage = 20)
    {
        $builder = $this->model->search($query)->active()->with(['tags', 'media']);

        if ($type) {
            $builder->byType($type);
        }

        return $builder->ordered()->paginate($perPage);
    }

    /**
     * Bulk update contact status
     */
    public function bulkUpdateStatus(array $contactIds, bool $isActive)
    {
        return $this->model->whereIn('id', $contactIds)
            ->update(['is_active' => $isActive]);
    }

    /**
     * Count active contacts
     */
    public function countActive()
    {
        return $this->model->active()->count();
    }

    /**
     * Get count by type
     */
    public function getCountByType()
    {
        return $this->model->active()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get count by tag
     */
    public function getCountByTag()
    {
        return $this->model->active()
            ->join('contact_book_tag', 'contact_books.id', '=', 'contact_book_tag.contact_book_id')
            ->join('tags', 'contact_book_tag.tag_id', '=', 'tags.id')
            ->selectRaw('tags.name, COUNT(*) as count')
            ->groupBy('tags.id', 'tags.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();
    }

    /**
     * Get recent contacts
     */
    public function getRecent(int $limit = 5)
    {
        return $this->model->active()
            ->with(['tags', 'media'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get contacts for export with filtering (no pagination)
     */
    public function getContactsForExport(array $params = [])
    {
        $query = $this->model->query()->with(['tags', 'media']);

        // Apply filters
        if (!empty($params['keyword'])) {
            $query->search($params['keyword']);
        }

        if (!empty($params['type'])) {
            $query->byType($params['type']);
        }

        if (!empty($params['tag_id'])) {
            $query->byTag($params['tag_id']);
        }

        if (isset($params['is_active'])) {
            if ($params['is_active']) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        } else {
            // Default to active contacts only
            $query->active();
        }

        return $query->ordered()->get();
    }

    /**
     * Legacy search method for backward compatibility
     */
    public function search(string $keyword, ?string $type = null)
    {
        $query = $this->model->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('phone', 'LIKE', "%{$keyword}%");
        });

        if ($type) {
            $query->where('type', $type);
        }

        return $query->paginate(15);
    }
}
