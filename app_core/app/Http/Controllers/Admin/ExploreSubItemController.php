<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExploreItem;
use App\Models\ExploreSubItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExploreSubItemController extends Controller
{
    public function index(ExploreItem $exploreItem)
    {
        $items = $exploreItem->subItems()->paginate(30);

        return view('admin.explore-items.items-index', [
            'explore' => $exploreItem,
            'items' => $items,
        ]);
    }

    public function create(ExploreItem $exploreItem)
    {
        return view('admin.explore-items.items-create', [
            'explore' => $exploreItem,
        ]);
    }

    public function store(Request $request, ExploreItem $exploreItem)
    {
        $data = $this->validateItem($request);

        $payload = [
            'explore_item_id' => $exploreItem->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'map_url' => $data['map_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('explore/items', 'public');
        }

        ExploreSubItem::create($payload);

        return redirect("/admin/explore-items/{$exploreItem->id}/items")->with('success', 'Item added.');
    }

    public function edit(ExploreItem $exploreItem, ExploreSubItem $item)
    {
        return view('admin.explore-items.items-edit', [
            'explore' => $exploreItem,
            'item' => $item,
        ]);
    }

    public function update(Request $request, ExploreItem $exploreItem, ExploreSubItem $item)
    {
        $data = $this->validateItem($request);

        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'map_url' => $data['map_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $payload['image'] = $request->file('image')->store('explore/items', 'public');
        } elseif ($request->boolean('remove_image') && $item->image) {
            Storage::disk('public')->delete($item->image);
            $payload['image'] = null;
        }

        $item->update($payload);

        return redirect("/admin/explore-items/{$exploreItem->id}/items")->with('success', 'Item updated.');
    }

    public function toggle(ExploreItem $exploreItem, ExploreSubItem $item)
    {
        $item->is_active = ! $item->is_active;
        $item->save();

        return back()->with('success', 'Item status updated.');
    }

    public function destroy(ExploreItem $exploreItem, ExploreSubItem $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return back()->with('success', 'Item deleted.');
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:190',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:190',
            'address' => 'nullable|string|max:500',
            'map_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_image' => 'nullable|boolean',
        ]);
    }
}
