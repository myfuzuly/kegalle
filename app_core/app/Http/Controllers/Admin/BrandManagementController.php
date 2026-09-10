<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::with('categories');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('category')) {
            $allIds = [(int) $request->category];

            // Expand downward — descendants
            $toExpand = $allIds;
            while (!empty($toExpand)) {
                $childIds = Category::whereIn('parent_id', $toExpand)->pluck('id')->toArray();
                $newIds = array_diff($childIds, $allIds);
                if (empty($newIds)) break;
                $allIds = array_merge($allIds, $newIds);
                $toExpand = $newIds;
            }

            // Expand upward — ancestors
            $node = Category::find((int) $request->category);
            while ($node && $node->parent_id) {
                if (!in_array($node->parent_id, $allIds)) {
                    $allIds[] = $node->parent_id;
                }
                $node = Category::find($node->parent_id);
            }

            $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $allIds));
        }

        $brands = $query->orderBy('name')->paginate(30)->withQueryString();
        $totalBrands = Brand::count();
        $categories  = Category::whereNull('parent_id')
            ->with(['children' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $brands->total(),
                'rows'       => view('admin.brands._rows', compact('brands'))->render(),
                'pagination' => (string) $brands->links('vendor.pagination.ka-admin'),
            ]);
        }

        return view('admin.brands.index', compact('brands', 'totalBrands', 'categories'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.brands.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'sort_order' => 'nullable|integer',
        ]);

        $brand = Brand::create([
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'category_group' => '',
            'sort_order'     => $data['sort_order'] ?? 0,
            'is_active'      => true,
        ]);

        $brand->categories()->sync($request->input('category_ids', []));

        return redirect('/admin/brands')->with('success', 'Brand "' . $data['name'] . '" created.');
    }

    public function edit(Brand $brand)
    {
        $brand->loadCount('models')->load('categories');
        $models     = BrandModel::where('brand_id', $brand->id)->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.brands.edit', compact('brand', 'models', 'categories'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $brand->update([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        $brand->categories()->sync($request->input('category_ids', []));

        return redirect('/admin/brands/' . $brand->id . '/edit')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        $name = $brand->name;
        $brand->models()->delete();
        $brand->delete();
        return redirect('/admin/brands')->with('success', 'Brand "' . $name . '" deleted.');
    }

    public function storeModel(Request $request)
    {
        $data = $request->validate([
            'brand_id'   => 'required|exists:brands,id',
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        BrandModel::create([
            'brand_id'   => $data['brand_id'],
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Model "' . $data['name'] . '" added.');
    }

    public function updateModel(Request $request, BrandModel $model)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $model->update([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Model updated.');
    }

    public function destroyModel(BrandModel $model)
    {
        $brandId = $model->brand_id;
        $model->delete();
        return redirect('/admin/brands/' . $brandId . '/edit')->with('success', 'Model deleted.');
    }

    public function byCategory()
    {
        // Load 3 levels: parent → sub → leaf, each with their directly assigned brands
        $parents = Category::whereNull('parent_id')
            ->with([
                'brands' => fn($q) => $q->orderBy('name')->select('brands.id','brands.name','brands.is_active'),
                'children' => fn($q) => $q->orderBy('sort_order')->orderBy('name')->with([
                    'brands' => fn($q2) => $q2->orderBy('name')->select('brands.id','brands.name','brands.is_active'),
                    'children' => fn($q2) => $q2->orderBy('sort_order')->orderBy('name')->with([
                        'brands' => fn($q3) => $q3->orderBy('name')->select('brands.id','brands.name','brands.is_active'),
                    ]),
                ]),
            ])
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        // Bubble up: leaf → sub → parent (deduplicated by brand id)
        foreach ($parents as $parent) {
            $parentPool = $parent->brands->keyBy('id');

            foreach ($parent->children as $sub) {
                $subPool = $sub->brands->keyBy('id');

                // Merge leaf brands up into sub
                foreach ($sub->children as $leaf) {
                    foreach ($leaf->brands as $b) {
                        $subPool->put($b->id, $b);
                    }
                }

                $sub->all_brands = $subPool->sortBy('name')->values();

                // Merge sub's full pool up into parent
                foreach ($subPool as $b) {
                    $parentPool->put($b->id, $b);
                }
            }

            $parent->all_brands = $parentPool->sortBy('name')->values();
        }

        $uncategorized = Brand::doesntHave('categories')->orderBy('name')->get();
        $totalBrands   = Brand::count();

        return view('admin.brands.by-category', compact('parents', 'uncategorized', 'totalBrands'));
    }

    public function toggleBrand(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);
        return back()->with('success', $brand->name . ' is now ' . ($brand->is_active ? 'active' : 'inactive') . '.');
    }
}
