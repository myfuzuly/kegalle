<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', 1)->withCount('listings')->take(9)->get();

        $hasEventsTable = true;
        try {
            \Illuminate\Support\Facades\DB::select('SELECT 1 FROM events LIMIT 1');
        } catch (\Throwable $e) {
            $hasEventsTable = false;
        }

        $events = collect();
        $featuredEvents = collect();
        $useRealEvents = false;

        if ($hasEventsTable) {
            $query = Event::upcoming()->with(['category', 'store', 'user']);

            if ($request->filled('q')) {
                $query->where('title', 'like', '%' . $request->q . '%');
            }
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }
            if ($request->filled('date_filter')) {
                $today = now()->toDateString();
                $query = match ($request->date_filter) {
                    'today' => $query->whereDate('event_date', $today),
                    'weekend' => $query->whereBetween('event_date', [now()->endOfWeek()->subDay(), now()->endOfWeek()]),
                    'month' => $query->whereMonth('event_date', now()->month)->whereYear('event_date', now()->year),
                    default => $query,
                };
            }
            if ($request->filled('price')) {
                $query = match ($request->price) {
                    'free' => $query->where('is_free', true),
                    '1-1000' => $query->where('is_free', false)->whereBetween('price', [1, 1000]),
                    '1000-5000' => $query->whereBetween('price', [1000, 5000]),
                    '5000+' => $query->where('price', '>', 5000),
                    default => $query,
                };
            }

            $sort = $request->input('sort', 'date');
            $query = match ($sort) {
                'popular' => $query->orderByDesc('views'),
                'price_low' => $query->orderBy('price'),
                'price_high' => $query->orderByDesc('price'),
                default => $query->orderBy('event_date'),
            };

            $events = $query->paginate(8)->withQueryString();
            $featuredEvents = Event::upcoming()->featured()->with(['category'])->orderBy('event_date')->take(4)->get();
            $useRealEvents = $events->total() > 0;
        }

        $locations = ['Kegalle', 'Mawanella', 'Warakapola', 'Rambukkana', 'Aranayake'];

        return view('frontend.events.index', compact(
            'categories', 'events', 'featuredEvents', 'useRealEvents', 'locations'
        ));
    }

    public function show($slug)
    {
        $event = null;
        $relatedEvents = collect();

        try {
            $event = Event::where('slug', $slug)->with(['category', 'store', 'user'])->first();
            if ($event) {
                $event->increment('views');
                $relatedEvents = Event::upcoming()
                    ->where('id', '!=', $event->id)
                    ->when($event->category_id, fn($q) => $q->where('category_id', $event->category_id))
                    ->orderBy('event_date')
                    ->take(4)
                    ->get();
            }
        } catch (\Throwable $e) {}

        $locations = ['Kegalle', 'Mawanella', 'Warakapola', 'Rambukkana', 'Aranayake'];

        return view('frontend.events.show', compact('event', 'relatedEvents', 'slug', 'locations'));
    }

    public function monthly(Request $request, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        $date = \Carbon\Carbon::createFromDate($year, $month, 1);

        $events = collect();
        try {
            $events = Event::published()
                ->whereYear('event_date', $year)
                ->whereMonth('event_date', $month)
                ->with(['category', 'store'])
                ->orderBy('event_date')
                ->paginate(12)
                ->withQueryString();
        } catch (\Throwable $e) {}

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $d = now()->addMonths($i);
            $months[] = ['date' => $d, 'label' => $d->format('F Y'), 'y' => $d->year, 'm' => $d->month];
        }

        $locations = ['Kegalle', 'Mawanella', 'Warakapola', 'Rambukkana', 'Aranayake'];

        return view('frontend.events.monthly', compact('events', 'date', 'year', 'month', 'months', 'locations'));
    }
}
