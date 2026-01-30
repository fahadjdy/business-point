<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    protected $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all active tags with optional filtering
     */
    public function index(Request $request)
    {
        $params = $request->validate([
            'category' => 'nullable|string|in:profession,skill,business,service',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $params['per_page'] ?? 50;

        return $this->success(
            $this->service->getPaginatedTags($perPage, $params),
            'Tags retrieved successfully'
        );
    }

    /**
     * Get all tags without pagination (for dropdowns)
     */
    public function all(Request $request)
    {
        $params = $request->validate([
            'category' => 'nullable|string|in:profession,skill,business,service',
            'search' => 'nullable|string|max:100',
        ]);

        return $this->success(
            $this->service->getAllTags($params),
            'Tags retrieved successfully'
        );
    }

    /**
     * Get tags grouped by category
     */
    public function grouped(Request $request)
    {
        $params = $request->validate([
            'search' => 'nullable|string|max:100',
        ]);

        return $this->success(
            $this->service->getTagsGroupedByCategory($params),
            'Grouped tags retrieved successfully'
        );
    }

    /**
     * Get tag statistics
     */
    public function stats()
    {
        return $this->success(
            $this->service->getTagStats(),
            'Tag statistics retrieved successfully'
        );
    }

    /**
     * Get popular tags (most used)
     */
    public function popular(Request $request)
    {
        $limit = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
        ])['limit'] ?? 10;

        return $this->success(
            $this->service->getPopularTags($limit),
            'Popular tags retrieved successfully'
        );
    }
}