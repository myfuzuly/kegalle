<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryPageController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', 1)
            ->withCount(['listings' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $parents = $categories->whereNull('parent_id')->values();
        $childMap = $categories->whereNotNull('parent_id')->groupBy('parent_id');

        return view('frontend.categories.index', compact('categories', 'parents', 'childMap'));
    }
}
