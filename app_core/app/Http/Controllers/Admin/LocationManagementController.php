<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LocationManagementController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::with('parent')
            ->when($request->q, function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('slug', 'like', '%'.$request->q.'%');
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $parents = Location::where('is_active', true)
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.locations.index', compact('locations', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:locations,id',
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:180|unique:locations,slug',
            'type' => 'required|in:province,district,city,town',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Location::create([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => ! empty($data['slug'])
                ? Str::slug($data['slug'])
                : Str::slug($data['name'].'-'.uniqid()),
            'type' => $data['type'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Location added successfully.');
    }

    public function edit(Location $location)
    {
        $parents = Location::where('id', '!=', $location->id)
            ->where('is_active', true)
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.locations.edit', compact('location', 'parents'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'parent_id' => [
                'nullable',
                'exists:locations,id',
                function ($attribute, $value, $fail) use ($location) {
                    if ((int) $value === (int) $location->id) {
                        $fail('A location cannot be its own parent.');
                    }
                },
            ],
            'name' => 'required|string|max:150',
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('locations', 'slug')->ignore($location->id),
            ],
            'type' => 'required|in:province,district,city,town',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $location->update([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'slug' => Str::slug($data['slug']),
            'type' => $data['type'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect('/admin/locations')->with('success', 'Location updated successfully.');
    }

    public function toggle(Location $location)
    {
        $location->is_active = ! (bool) $location->is_active;
        $location->save();

        return back()->with('success', 'Location status updated.');
    }

    public function destroy(Location $location)
    {
        if ($location->children()->count() > 0) {
            return back()->with('success', 'This location has child locations. Delete child locations first.');
        }

        $location->delete();

        return back()->with('success', 'Location deleted successfully.');
    }
}
