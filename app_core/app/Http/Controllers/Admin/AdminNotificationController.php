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
            return redirect($notification->link);
        }

        return back();
    }

    public function markAllRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
