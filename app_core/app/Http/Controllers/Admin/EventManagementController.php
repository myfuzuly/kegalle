<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['user', 'category', 'store'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%$q%")
                    ->orWhere('location', 'like', "%$q%")
                    ->orWhere('venue', 'like', "%$q%");
            });
        }

        $events = $query->paginate(20)->withQueryString();
        $pendingCount = Event::where('status', 'pending')->count();

        if ($request->ajax()) {
            return response()->json([
                'total'      => $events->total(),
                'rows'       => view('admin.events._rows', compact('events'))->render(),
                'pagination' => (string) $events->links('vendor.pagination.ka-admin'),
            ]);
        }

        return view('admin.events.index', compact('events', 'pendingCount'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $stores = Store::orderBy('name')->limit(500)->get();
        $users = User::orderBy('name')->limit(500)->get();

        return view('admin.events.create', compact('categories', 'locations', 'stores', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        $data = $request->only([
            'title', 'description', 'event_date', 'starts_at', 'ends_at',
            'location', 'venue', 'price', 'event_type', 'capacity',
            'organizer_name', 'organizer_phone', 'organizer_email', 'poster_type',
            'user_id', 'category_id', 'store_id', 'status', 'admin_note',
        ]);

        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        $data['is_free'] = $request->boolean('is_free');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['price'] = $data['is_free'] ? 0 : ($data['price'] ?? 0);
        $data['status'] = $data['status'] ?? 'published';
        $data['poster_type'] = $data['poster_type'] ?? 'user';
        // If independent poster, clear user/store
        if (($data['poster_type'] ?? '') === 'independent') {
            $data['user_id'] = null;
            $data['store_id'] = null;
        }

        Event::create($data);

        return redirect('/admin/events')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $stores = Store::orderBy('name')->limit(500)->get();
        $users = User::orderBy('name')->limit(500)->get();

        return view('admin.events.edit', compact('event', 'categories', 'locations', 'stores', 'users'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        $data = $request->only([
            'title', 'description', 'event_date', 'starts_at', 'ends_at',
            'location', 'venue', 'price', 'event_type', 'capacity',
            'organizer_name', 'organizer_phone', 'organizer_email', 'poster_type',
            'user_id', 'category_id', 'store_id', 'status', 'admin_note',
        ]);

        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        $data['is_free'] = $request->boolean('is_free');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['price'] = $data['is_free'] ? 0 : ($data['price'] ?? 0);

        $event->update($data);

        return redirect('/admin/events')->with('success', 'Event updated successfully.');
    }

    public function approve(Event $event)
    {
        $event->update(['status' => 'published']);

        return back()->with('success', "Event #{$event->id} approved & published.");
    }

    public function reject(Event $event, Request $request)
    {
        $event->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note', 'Rejected by admin.'),
        ]);

        return back()->with('success', "Event #{$event->id} rejected.");
    }

    public function feature(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);

        $label = $event->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Event #{$event->id} $label.");
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return back()->with('success', 'Event deleted.');
    }
}
