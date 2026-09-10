<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryPageController extends Controller
{
    public function index()
    {
        [$parents, $subMap, $leafMap, $topCats] = cache()->remember('categories_page_data', 600, function () {
            $all = Category::query()
                ->where('is_active', 1)
                ->withCount(['listings' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $parents_raw = $all->whereNull('parent_id');
            $subMap  = $all->whereNotNull('parent_id')
                           ->filter(fn ($c) => $parents_raw->contains('id', $c->parent_id))
                           ->groupBy('parent_id');

            $leafMap = $all->whereNotNull('parent_id')
                           ->filter(fn ($c) => ! $parents_raw->contains('id', $c->parent_id))
                           ->groupBy('parent_id');

            // Order parent categories by total descendant count (subs + leaves) descending
            // so the grid fills elegantly with large categories first, small ones at the end
            $parents = $parents_raw->sortByDesc(function ($m) use ($subMap, $leafMap) {
                $subs = $subMap->get($m->id, collect());
                return $subs->count() + $subs->sum(fn ($s) => $leafMap->get($s->id, collect())->count());
            })->values();

            $topCats = $parents->sortByDesc(function ($m) use ($subMap, $leafMap) {
                $subTotal = $subMap->get($m->id, collect())->sum(fn ($s) =>
                    $s->listings_count + $leafMap->get($s->id, collect())->sum('listings_count')
                );
                return $m->listings_count + $subTotal;
            })->take(8);

            return [$parents, $subMap, $leafMap, $topCats];
        });

        return view('frontend.categories.index', compact('parents', 'subMap', 'leafMap', 'topCats'));
    }
}
