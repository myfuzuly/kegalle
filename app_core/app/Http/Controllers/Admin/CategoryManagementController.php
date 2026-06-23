<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryManagementController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent'])->withCount('listings')
            ->orderBy('parent_id')->orderBy('sort_order')->orderBy('name')->paginate(30);
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:180|unique:categories,slug',
            'icon' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        Category::create([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
            'icon' => $data['icon'] ?? '🛒',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)->whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => ['required', 'string', 'max:180', Rule::unique('categories', 'slug')->ignore($category->id)],
            'icon' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['slug']),
            'icon' => $data['icon'] ?? $category->icon,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect('/admin/categories')->with('success', 'Category updated successfully.');
    }

    public function toggle(Category $category)
    {
        $category->is_active = ! (bool) $category->is_active;
        $category->save();

        return back()->with('success', 'Category status updated.');
    }

    public function destroy(Category $category)
    {
        if (method_exists($category, 'children') && $category->children()->count() > 0) {
            return back()->with('success', 'This category has sub-categories. Delete or move sub-categories first.');
        }
        if (method_exists($category, 'listings') && $category->listings()->count() > 0) {
            return back()->with('success', 'Category has listings and cannot be deleted. Deactivate it instead.');
        }
        $category->delete();

        return back()->with('success','Category deleted successfully.');
    }
}
