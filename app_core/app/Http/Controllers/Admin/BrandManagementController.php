<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount('models');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $brands = $query->orderBy('name')->paginate(30)->withQueryString();
        $totalBrands = Brand::count();

        return view('admin.brands.index', compact('brands', 'totalBrands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'sort_order' => 'nullable|integer',
        ]);

        Brand::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'category_group' => '',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect('/admin/brands')->with('success', 'Brand "' . $data['name'] . '" created.');
    }

    public function edit(Brand $brand)
    {
        $brand->loadCount('models');
        $models = BrandModel::where('brand_id', $brand->id)
            ->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.brands.edit', compact('brand', 'models'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $brand->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

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
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        BrandModel::create([
            'brand_id' => $data['brand_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Model "' . $data['name'] . '" added.');
    }

    public function updateModel(Request $request, BrandModel $model)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $model->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Model updated.');
    }

    public function destroyModel(BrandModel $model)
    {
        $brandId = $model->brand_id;
        $model->delete();
        return redirect('/admin/brands/' . $brandId . '/edit')->with('success', 'Model deleted.');
    }

    public function toggleBrand(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);
        return back()->with('success', $brand->name . ' is now ' . ($brand->is_active ? 'active' : 'inactive') . '.');
    }
}
