<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AdminNotification::query()
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->status === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($request->status === 'read', fn ($q) => $q->where('is_read', true))
            ->latest()->paginate(30)->withQueryString();

        $types = AdminNotification::select('type')->distinct()->orderBy('type')->pluck('type');
        $unreadCount = AdminNotification::where('is_read', false)->count();

        return view('admin.notifications.index', compact('notifications', 'types', 'unreadCount'));
    }

    public function markRead(AdminNotification $notification)
    {
        $notification->update(['is_read' => true]);

        if ($notification->link) {
            $link = $notification->link;
            $scheme = parse_url($link, PHP_URL_SCHEME);
            if ($scheme !== null && !in_array($scheme, ['http', 'https'])) {
                return back();
            }
            $host = parse_url($link, PHP_URL_HOST);
            if ($host && $host !== parse_url(config('app.url'), PHP_URL_HOST)) {
                return back();
            }
            return redirect($link);
        }

        return back();
    }

    public function markAllRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
