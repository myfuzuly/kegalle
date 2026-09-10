<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['user', 'category'])
            ->latest()
            ->paginate(40);

        return view('admin.services.index', compact('services'));
    }

    public function approve(Service $service)
    {
        $service->update(['status' => 'approved']);
        $service->load('user');

        if ($service->user_id) {
            \App\Models\UserNotification::send(
                $service->user_id,
                'service_approved',
                'Your service has been approved!',
                "Your service \"{$service->title}\" is now live on Kegalle Marketplace.",
                '/services/' . $service->slug,
            );
            app(\App\Services\WebPushService::class)->notifyUser(
                $service->user_id,
                '✅ Service approved!',
                '"' . \Str::limit($service->title, 50) . '" is now live on kegalle.',
                url('/services/' . $service->slug)
            );
        }

        return back()->with('success', "Service \"{$service->title}\" approved.");
    }

    public function reject(Service $service)
    {
        $service->update(['status' => 'rejected']);
        $service->load('user');

        if ($service->user_id) {
            \App\Models\UserNotification::send(
                $service->user_id,
                'service_rejected',
                'Service not approved',
                "Your service \"{$service->title}\" needs changes before it can go live.",
                '/dashboard/services',
            );
            app(\App\Services\WebPushService::class)->notifyUser(
                $service->user_id,
                'Service not approved',
                '"' . \Str::limit($service->title, 50) . '" needs changes before it can go live.',
                url('/dashboard/services')
            );
        }

        return back()->with('success', "Service \"{$service->title}\" rejected.");
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted.');
    }
}
