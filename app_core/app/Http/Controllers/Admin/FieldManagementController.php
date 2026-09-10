<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FieldManagementController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'fields');

        $fields = CustomField::withCount('categories')->orderBy('sort_order')->orderBy('label')->get();

        $categories = Category::with(['parent', 'customFields'])
            ->whereNotNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();

        $parents = Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.listing-fields.index', compact('fields', 'categories', 'parents', 'tab'));
    }

    // ── Custom Fields CRUD ──────────────────────────────────────

    public function storeField(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:select,text,number,checkbox_group,brand_select,model_select,pill_group,text_unit',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string|max:150',
            'unit' => 'nullable|string|max:20',
            'multi' => 'nullable|boolean',
            'full_width' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'is_searchable' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $optionTypes = ['select', 'checkbox_group', 'pill_group'];
        $options = null;
        if (in_array($data['type'], $optionTypes) && !empty($data['options'])) {
            $options = array_map('trim', explode(',', $data['options']));
        }

        CustomField::create([
            'label' => $data['label'],
            'name' => Str::snake(Str::ascii($data['label'])),
            'type' => $data['type'],
            'options' => $options,
            'placeholder' => $data['placeholder'] ?? null,
            'unit' => $data['unit'] ?? null,
            'multi' => $request->boolean('multi'),
            'full_width' => $request->boolean('full_width'),
            'is_required' => $request->boolean('is_required'),
            'is_searchable' => $request->boolean('is_searchable'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Field created.');
    }

    public function updateField(Request $request, CustomField $field)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:select,text,number,checkbox_group,brand_select,model_select,pill_group,text_unit',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string|max:150',
            'unit' => 'nullable|string|max:20',
            'multi' => 'nullable|boolean',
            'full_width' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'is_searchable' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $optionTypes = ['select', 'checkbox_group', 'pill_group'];
        $options = null;
        if (in_array($data['type'], $optionTypes) && !empty($data['options'])) {
            $options = array_map('trim', explode(',', $data['options']));
        }

        $field->update([
            'label' => $data['label'],
            'type' => $data['type'],
            'options' => $options,
            'placeholder' => $data['placeholder'] ?? null,
            'unit' => $data['unit'] ?? null,
            'multi' => $request->boolean('multi'),
            'full_width' => $request->boolean('full_width'),
            'is_required' => $request->boolean('is_required'),
            'is_searchable' => $request->boolean('is_searchable'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Field updated.');
    }

    public function destroyField(CustomField $field)
    {
        $field->categories()->detach();
        $field->delete();
        return back()->with('success', 'Field deleted.');
    }

    // ── Category ↔ Field assignments ────────────────────────────

    public function assignFields(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'field_ids' => 'nullable|array',
            'field_ids.*' => 'exists:custom_fields,id',
        ]);

        $category = Category::findOrFail($data['category_id']);
        $fieldIds = $data['field_ids'] ?? [];

        $sync = [];
        foreach ($fieldIds as $i => $fid) {
            $sync[$fid] = ['sort_order' => $i, 'is_required' => false, 'show_in_filter' => true, 'show_in_list' => true];
        }
        $category->customFields()->sync($sync);

        return back()->with('success', 'Fields assigned to ' . $category->name . '.');
    }

    public function getCategoryFields(Category $category)
    {
        $assigned = $category->customFields()->pluck('custom_fields.id')->toArray();
        return response()->json(['assigned' => $assigned]);
    }

    // ── Brands CRUD ─────────────────────────────────────────────

    public function storeBrand(Request $request)
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

        return back()->with('success', 'Brand created.');
    }

    public function updateBrand(Request $request, Brand $brand)
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

        return back()->with('success', 'Brand updated.');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->models()->delete();
        $brand->delete();
        return back()->with('success', 'Brand and its models deleted.');
    }

    // ── Models CRUD ─────────────────────────────────────────────

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

        return back()->with('success', 'Model created.');
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
        $model->delete();
        return back()->with('success', 'Model deleted.');
    }

    public function getBrandModels(Brand $brand)
    {
        return response()->json($brand->models()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'sort_order', 'is_active']));
    }
}
