<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GovernmentService;

class GovernmentServiceController extends Controller
{
    public function index()
    {
        $services = GovernmentService::active()->orderBy('sort_order')->get();

        return view('pages.government-services', compact('services'));
    }

    public function show(string $slug)
    {
        $service = GovernmentService::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $items = $service->activeItems()->get();

        return view('pages.government-service-show', compact('service', 'items'));
    }
}
