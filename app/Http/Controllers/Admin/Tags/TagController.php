<?php

namespace App\Http\Controllers\Admin\Tags;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $perPage = $request->query('per_page', 50);

        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginatedTags($perPage, $params)
        ]);
    }

    public function all(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAllTags($request->all())
        ]);
    }

    public function grouped(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getTagsGroupedByCategory($request->all())
        ]);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getTagStats()
        ]);
    }

    public function popular(Request $request)
    {
        $limit = $request->query('limit', 10);
        return response()->json([
            'success' => true,
            'data' => $this->service->getPopularTags($limit)
        ]);
    }
}
