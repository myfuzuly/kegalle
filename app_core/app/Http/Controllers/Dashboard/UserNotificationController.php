<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->when($request->status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($request->status === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $notifications->total(),
                'rows'       => view('dashboard.notifications._rows', compact('notifications'))->render(),
                'pagination' => (string) $notifications->links('vendor.pagination.dashboard'),
            ]);
        }

        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function markRead(UserNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
