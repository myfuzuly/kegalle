<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentService;
use App\Models\GovernmentServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GovernmentServiceItemController extends Controller
{
    public function index(GovernmentService $governmentService)
    {
        $items = $governmentService->items()->paginate(30);

        return view('admin.government-services.items-index', [
            'service' => $governmentService,
            'items' => $items,
        ]);
    }

    public function create(GovernmentService $governmentService)
    {
        return view('admin.government-services.items-create', [
            'service' => $governmentService,
        ]);
    }

    public function store(Request $request, GovernmentService $governmentService)
    {
        $data = $this->validateItem($request);

        $payload = [
            'government_service_id' => $governmentService->id,
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
            $payload['image'] = $request->file('image')->store('government-services/items', 'public');
        }

        GovernmentServiceItem::create($payload);

        return redirect("/admin/government-services/{$governmentService->id}/items")->with('success', 'Item added.');
    }

    public function edit(GovernmentService $governmentService, GovernmentServiceItem $item)
    {
        return view('admin.government-services.items-edit', [
            'service' => $governmentService,
            'item' => $item,
        ]);
    }

    public function update(Request $request, GovernmentService $governmentService, GovernmentServiceItem $item)
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
            $payload['image'] = $request->file('image')->store('government-services/items', 'public');
        } elseif ($request->boolean('remove_image') && $item->image) {
            Storage::disk('public')->delete($item->image);
            $payload['image'] = null;
        }

        $item->update($payload);

        return redirect("/admin/government-services/{$governmentService->id}/items")->with('success', 'Item updated.');
    }

    public function toggle(GovernmentService $governmentService, GovernmentServiceItem $item)
    {
        $item->is_active = ! $item->is_active;
        $item->save();

        return back()->with('success', 'Item status updated.');
    }

    public function destroy(GovernmentService $governmentService, GovernmentServiceItem $item)
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
