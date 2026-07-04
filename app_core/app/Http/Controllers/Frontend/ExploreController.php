<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ExploreItem;

class ExploreController extends Controller
{
    public function index()
    {
        $exploreItems = ExploreItem::active()->orderBy('sort_order')->get();

        return view('pages.explore-index', compact('exploreItems'));
    }

    public function show(string $slug)
    {
        $explore = ExploreItem::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $items = $explore->activeSubItems()->get();

        return view('pages.explore-show', compact('explore', 'items'));
    }
}
