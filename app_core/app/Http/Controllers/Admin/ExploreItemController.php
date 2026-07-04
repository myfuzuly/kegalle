<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExploreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExploreItemController extends Controller
{
    public function index()
    {
        $items = ExploreItem::withCount('subItems')->orderBy('sort_order')->latest()->paginate(20);

        return view('admin.explore-items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.explore-items.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateItem($request);

        $payload = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? '📍',
            'gradient_start' => $data['gradient_start'] ?? '#1B5E20',
            'gradient_end' => $data['gradient_end'] ?? '#388E3C',
            'items' => $data['items'] ?? null,
            'content' => $data['content'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'link_label' => $data['link_label'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('explore', 'public');
        }

        ExploreItem::create($payload);

        return redirect('/admin/explore-items')->with('success', 'Explore item created successfully.');
    }

    public function edit(ExploreItem $exploreItem)
    {
        return view('admin.explore-items.edit', ['item' => $exploreItem]);
    }

    public function update(Request $request, ExploreItem $exploreItem)
    {
        $data = $this->validateItem($request);

        $payload = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? $exploreItem->icon,
            'gradient_start' => $data['gradient_start'] ?? $exploreItem->gradient_start,
            'gradient_end' => $data['gradient_end'] ?? $exploreItem->gradient_end,
            'items' => $data['items'] ?? null,
            'content' => $data['content'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'link_label' => $data['link_label'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($exploreItem->image) {
                Storage::disk('public')->delete($exploreItem->image);
            }
            $payload['image'] = $request->file('image')->store('explore', 'public');
        } elseif ($request->boolean('remove_image') && $exploreItem->image) {
            Storage::disk('public')->delete($exploreItem->image);
            $payload['image'] = null;
        }

        $exploreItem->update($payload);

        return redirect('/admin/explore-items')->with('success', 'Explore item updated successfully.');
    }

    public function toggle(ExploreItem $exploreItem)
    {
        $exploreItem->is_active = ! $exploreItem->is_active;
        $exploreItem->save();

        return back()->with('success', 'Explore item status updated.');
    }

    public function destroy(ExploreItem $exploreItem)
    {
        if ($exploreItem->image) {
            Storage::disk('public')->delete($exploreItem->image);
        }
        $exploreItem->delete();

        return back()->with('success', 'Explore item deleted.');
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:10',
            'gradient_start' => 'nullable|string|max:9',
            'gradient_end' => 'nullable|string|max:9',
            'items' => 'nullable|string|max:1000',
            'content' => 'nullable|string|max:10000',
            'link_url' => 'nullable|string|max:255',
            'link_label' => 'nullable|string|max:60',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_image' => 'nullable|boolean',
        ]);
    }
}
