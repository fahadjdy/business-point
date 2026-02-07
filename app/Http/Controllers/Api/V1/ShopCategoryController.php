<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShopCategory;

class ShopCategoryController extends Controller
{
    public function index()
    {
        $categories = ShopCategory::where('is_active', true)->select('id', 'name', 'slug')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
