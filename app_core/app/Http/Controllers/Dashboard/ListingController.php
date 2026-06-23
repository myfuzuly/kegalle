<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::query()
            ->with(['category', 'store'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Listing::where('user_id', auth()->id())->count(),
            'approved' => Listing::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'pending' => Listing::where('user_id', auth()->id())->whereIn('status', ['pending', 'draft'])->count(),
            'featured' => Listing::where('user_id', auth()->id())->where('is_featured', 1)->count(),
        ];

        return view('dashboard.listings.index', compact('listings', 'stats'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();
        $stores = Store::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'active', 'published'])
            ->orderBy('name')
            ->get();

        return view('dashboard.listings.create', compact('categories', 'stores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'type' => ['required', 'in:product,classified'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:190'],
            'description' => ['required', 'string', 'min:5'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (($data['type'] ?? null) === 'classified') {
            $data['store_id'] = null;
        }

        $slugBase = Str::slug($data['title']);
        $slug = $slugBase;
        $i = 2;
        while (Listing::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$i++;
        }

        $listing = Listing::create([
            'user_id' => auth()->id(),
            'store_id' => $data['store_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'],
            'price' => $data['price'] ?? 0,
            'location' => $data['location'] ?? 'Kegalle',
            'type' => $data['type'],
            'status' => 'pending',
            'is_featured' => false,
            'is_top' => false,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings', 'public');

                if (class_exists('App\\Models\\ListingImage')) {
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'path' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect('/dashboard/listings')->with('success', 'Listing submitted for admin approval.');
    }
}
