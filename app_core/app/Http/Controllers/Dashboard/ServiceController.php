<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('dashboard.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('dashboard.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:180',
            'category_id'      => 'nullable|exists:categories,id',
            'description'      => 'nullable|string|max:5000',
            'service_type'     => 'required|string|max:80',
            'pricing_model'    => 'required|in:hourly,fixed,negotiable,free_quote',
            'price'            => 'nullable|numeric|min:0|max:9999999',
            'location'         => 'required|string|max:100',
            'areas_covered'    => 'nullable|string|max:500',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'phone'            => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'image'            => 'nullable|file|mime_types:image/jpeg,image/png,image/webp|max:4096',
        ]);

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';
        $data['experience_years'] = $data['experience_years'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service submitted for admin approval.');
    }

    public function edit(Service $service)
    {
        abort_if($service->user_id !== auth()->id(), 403);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('dashboard.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        abort_if($service->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'title'            => 'required|string|max:180',
            'category_id'      => 'nullable|exists:categories,id',
            'description'      => 'nullable|string|max:5000',
            'service_type'     => 'required|string|max:80',
            'pricing_model'    => 'required|in:hourly,fixed,negotiable,free_quote',
            'price'            => 'nullable|numeric|min:0|max:9999999',
            'location'         => 'required|string|max:100',
            'areas_covered'    => 'nullable|string|max:500',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'phone'            => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'image'            => 'nullable|file|mime_types:image/jpeg,image/png,image/webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($service->image) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $data['status'] = 'pending';
        $service->update($data);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service updated and sent for re-approval.');
    }

    public function destroy(Service $service)
    {
        abort_if($service->user_id !== auth()->id(), 403);
        if ($service->image) Storage::disk('public')->delete($service->image);
        $service->delete();
        return redirect()->route('dashboard.services.index')->with('success', 'Service deleted.');
    }
}
