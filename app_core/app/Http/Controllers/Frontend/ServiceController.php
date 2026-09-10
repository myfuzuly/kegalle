<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::approved()->with(['user', 'category']);

        if ($request->q) {
            $query->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->type) {
            $query->where('service_type', $request->type);
        }
        if ($request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $services   = $query->latest()->paginate(18)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('frontend.services.index', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        abort_if($service->status !== 'approved', 404);

        $service->increment('views');
        $service->load(['user', 'category']);

        $otherServices = Service::approved()
            ->where('user_id', $service->user_id)
            ->where('id', '!=', $service->id)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.services.show', compact('service', 'otherServices'));
    }
}
