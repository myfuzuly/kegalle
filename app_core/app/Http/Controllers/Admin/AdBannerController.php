<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdBannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = AdBanner::when($request->location, fn ($q) => $q->where('location', $request->location))
            ->orderBy('location')->orderBy('sort_order')->latest()->paginate(20)->withQueryString();
        $locations = AdBanner::LOCATIONS;

        return view('admin.ad-banners.index', compact('banners', 'locations'));
    }

    public function create()
    {
        $locations = AdBanner::LOCATIONS;

        return view('admin.ad-banners.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $this->validateBanner($request);

        $payload = [
            'title' => $data['title'],
            'link_url' => $data['link_url'] ?? null,
            'location' => $data['location'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = \App\Helpers\ImageHelper::finalize(
                $request->file('image')->store('ad-banners', 'public')
            );
        }

        AdBanner::create($payload);

        return redirect('/admin/ad-banners')->with('success', 'Ad banner created successfully.');
    }

    public function edit(AdBanner $adBanner)
    {
        $locations = AdBanner::LOCATIONS;

        return view('admin.ad-banners.edit', ['banner' => $adBanner, 'locations' => $locations]);
    }

    public function update(Request $request, AdBanner $adBanner)
    {
        $data = $this->validateBanner($request);

        $payload = [
            'title' => $data['title'],
            'link_url' => $data['link_url'] ?? null,
            'location' => $data['location'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($adBanner->image) {
                Storage::disk('public')->delete($adBanner->image);
            }
            $payload['image'] = \App\Helpers\ImageHelper::finalize(
                $request->file('image')->store('ad-banners', 'public')
            );
        } elseif ($request->boolean('remove_image') && $adBanner->image) {
            Storage::disk('public')->delete($adBanner->image);
            $payload['image'] = null;
        }

        $adBanner->update($payload);

        return redirect('/admin/ad-banners')->with('success', 'Ad banner updated successfully.');
    }

    public function toggle(AdBanner $adBanner)
    {
        $adBanner->is_active = ! $adBanner->is_active;
        $adBanner->save();

        return back()->with('success', 'Ad banner status updated.');
    }

    public function destroy(AdBanner $adBanner)
    {
        if ($adBanner->image) {
            Storage::disk('public')->delete($adBanner->image);
        }
        $adBanner->delete();

        return back()->with('success', 'Ad banner deleted.');
    }

    private function validateBanner(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:160',
            'link_url' => 'nullable|url|max:255',
            'location' => 'required|string|in:'.implode(',', array_keys(AdBanner::LOCATIONS)),
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
            'remove_image' => 'nullable|boolean',
        ]);
    }
}
