<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryManagementController extends Controller
{
    public function index(Request $request)
    {
        $search   = trim($request->get('search', ''));
        $parentId = $request->get('parent_id');   // null means root view

        // Build breadcrumb for drill-down navigation
        $breadcrumb    = [];
        $currentParent = null;

        if ($parentId !== null) {
            $currentParent = Category::with('parent')->find($parentId);
            if ($currentParent) {
                if ($currentParent->parent_id) {
                    $grandparent = $currentParent->parent;
                    if ($grandparent?->parent_id) {
                        $breadcrumb[] = ['name' => $grandparent->parent->name ?? '', 'id' => $grandparent->parent_id];
                    }
                    $breadcrumb[] = ['name' => $grandparent->name, 'id' => $grandparent->id];
                }
                $breadcrumb[] = ['name' => $currentParent->name, 'id' => $currentParent->id];
            }
        }

        $query = Category::with(['parent'])->withCount(['listings', 'children']);

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        } elseif ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(50)->withQueryString();

        // Aggregate stats (fast on 1547 rows)
        $total     = Category::count();
        $mainCount = Category::whereNull('parent_id')->count();
        $stats = [
            'total' => $total,
            'main'  => $mainCount,
            'other' => $total - $mainCount,
        ];

        $mainCategories = Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);

        return view('admin.categories.index', compact(
            'categories', 'stats', 'breadcrumb', 'currentParent',
            'mainCategories', 'search', 'parentId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name'      => 'required|string|max:150',
            'slug'      => 'nullable|string|max:180|unique:categories,slug',
            'icon'      => 'nullable|string|max:20',
            'sort_order'=> 'nullable|integer',
            'image'     => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $payload = [
            'parent_id'  => $data['parent_id'] ?? null,
            'name'       => $data['name'],
            'slug'       => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
            'icon'       => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ];

        if (Schema::hasColumn('categories', 'image') && $request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($payload);

        // Return to the same level in the drill-down
        $redirect = '/admin/categories';
        if ($payload['parent_id']) {
            $parent = Category::find($payload['parent_id']);
            $redirect .= $parent?->parent_id
                ? '?parent_id=' . $payload['parent_id']
                : '?parent_id=' . $payload['parent_id'];
        }

        return redirect($redirect)->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $mainCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')->orderBy('name')
            ->get(['id', 'name']);

        // Sub-cats for the selected main (if editing a leaf)
        $subCategories = collect();
        if ($category->parent_id) {
            $parent = $category->parent;
            if ($parent && $parent->parent_id) {
                // category is a leaf — load sibling subs under the grandparent
                $subCategories = Category::where('parent_id', $parent->parent_id)
                    ->orderBy('sort_order')->orderBy('name')
                    ->get(['id', 'name', 'parent_id']);
            }
        }

        return view('admin.categories.edit', compact('category', 'mainCategories', 'subCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'parent_id'    => 'nullable|exists:categories,id',
            'name'         => 'required|string|max:150',
            'slug'         => ['required', 'string', 'max:180', Rule::unique('categories', 'slug')->ignore($category->id)],
            'icon'         => 'nullable|string|max:20',
            'sort_order'   => 'nullable|integer',
            'image'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_image' => 'nullable|boolean',
        ]);

        $payload = [
            'parent_id'  => $data['parent_id'] ?? null,
            'name'       => $data['name'],
            'slug'       => Str::slug($data['slug']),
            'icon'       => $data['icon'] ?? $category->icon,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ];

        if (Schema::hasColumn('categories', 'image')) {
            if ($request->hasFile('image')) {
                if ($category->image) Storage::disk('public')->delete($category->image);
                $payload['image'] = $request->file('image')->store('categories', 'public');
            } elseif ($request->boolean('remove_image') && $category->image) {
                Storage::disk('public')->delete($category->image);
                $payload['image'] = null;
            }
        }

        $category->update($payload);

        return redirect('/admin/categories')->with('success', 'Category updated.');
    }

    public function toggle(Category $category)
    {
        $category->is_active = ! (bool) $category->is_active;
        $category->save();

        return back()->with('success', 'Category status updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Delete or move sub-categories first.');
        }
        if ($category->listings()->count() > 0) {
            return back()->with('error', 'Category has listings — deactivate instead of deleting.');
        }
        if (Schema::hasColumn('categories', 'image') && $category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
