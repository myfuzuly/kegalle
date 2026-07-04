<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GovernmentServiceController extends Controller
{
    public function index()
    {
        $services = GovernmentService::withCount('items')->orderBy('sort_order')->latest()->paginate(20);

        return view('admin.government-services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.government-services.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateService($request);

        $payload = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? '🏛️',
            'icon_bg_start' => $data['icon_bg_start'] ?? '#1e6b3a',
            'icon_bg_end' => $data['icon_bg_end'] ?? '#2e9b5a',
            'content' => $data['content'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'map_url' => $data['map_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('government-services', 'public');
        }

        GovernmentService::create($payload);

        return redirect('/admin/government-services')->with('success', 'Government service created.');
    }

    public function edit(GovernmentService $governmentService)
    {
        return view('admin.government-services.edit', ['service' => $governmentService]);
    }

    public function update(Request $request, GovernmentService $governmentService)
    {
        $data = $this->validateService($request);

        $payload = [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? $governmentService->icon,
            'icon_bg_start' => $data['icon_bg_start'] ?? $governmentService->icon_bg_start,
            'icon_bg_end' => $data['icon_bg_end'] ?? $governmentService->icon_bg_end,
            'content' => $data['content'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'map_url' => $data['map_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($governmentService->image) {
                Storage::disk('public')->delete($governmentService->image);
            }
            $payload['image'] = $request->file('image')->store('government-services', 'public');
        } elseif ($request->boolean('remove_image') && $governmentService->image) {
            Storage::disk('public')->delete($governmentService->image);
            $payload['image'] = null;
        }

        $governmentService->update($payload);

        return redirect('/admin/government-services')->with('success', 'Government service updated.');
    }

    public function toggle(GovernmentService $governmentService)
    {
        $governmentService->is_active = ! $governmentService->is_active;
        $governmentService->save();

        return back()->with('success', 'Service status updated.');
    }

    public function destroy(GovernmentService $governmentService)
    {
        if ($governmentService->image) {
            Storage::disk('public')->delete($governmentService->image);
        }
        $governmentService->delete();

        return back()->with('success', 'Government service deleted.');
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:10',
            'icon_bg_start' => 'nullable|string|max:9',
            'icon_bg_end' => 'nullable|string|max:9',
            'content' => 'nullable|string|max:10000',
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
