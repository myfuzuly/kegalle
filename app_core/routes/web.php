<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\WholesalePriceController as AdminWholesalePriceController;
use App\Http\Controllers\WholesalePriceController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\FieldManagementController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\LocationManagementController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\AdminStoreController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminAiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Dashboard\ListingController as DashListing;
use App\Http\Controllers\Dashboard\StoreController as DashStore;
use App\Http\Controllers\Dashboard\StoreProductController;
use App\Http\Controllers\Dashboard\MembershipUpgradeController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ListingController;
use App\Http\Controllers\Frontend\StoreController;
use Illuminate\Support\Facades\Route;

// Cache-clear utility — requires CACHE_CLEAR_TOKEN env var (min 32 chars)
Route::get('/kcc', function () {
    $secret = env('CACHE_CLEAR_TOKEN', '');
    if (empty($secret) || strlen($secret) < 32 || !hash_equals($secret, (string) request('t'))) {
        abort(403);
    }
    $dir = storage_path('framework/pagecache');
    $n = 0;
    if (is_dir($dir)) {
        foreach (glob($dir . '/*.{html,lock}', GLOB_BRACE) ?: [] as $f) {
            @unlink($f); $n++;
        }
    }
    if (function_exists('opcache_reset')) opcache_reset();
    return "OK — cleared $n cache files. <a href='/'>Go home</a>";
})->middleware('throttle:5,10');


/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('cache.page:1800');
Route::get('/sitemap.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-listings.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'listings'])->name('sitemap.listings');
Route::get('/sitemap-stores.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'stores'])->name('sitemap.stores');
Route::get('/sitemap-events.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'events'])->name('sitemap.events');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemap-towns.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'towns'])->name('sitemap.towns');

Route::get('/ads/{adBanner}/click', function (\App\Models\AdBanner $adBanner) {
    $adBanner->increment('clicks');

    $url = $adBanner->link_url ?: url('/');
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if (!in_array($scheme, ['http', 'https'])) {
        return redirect('/');
    }
    return redirect()->away($url);
})->name('ads.click');

Route::get('/stores', [StoreController::class, 'index'])->name('stores.index')->middleware('cache.page:300');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('stores.show')->middleware('cache.page:300');
Route::get('/stores/{slug}', fn($slug) => redirect('/store/'.$slug, 301));

Route::redirect('/listings/create', '/dashboard/listings/create')->name('listings.create.redirect');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index')->middleware('cache.page:120');
Route::get('/classified', [ListingController::class, 'index'])->defaults('type', 'classified')->name('classified.index')->middleware('cache.page:120');
Route::get('/listing/{slug}', [ListingController::class, 'show'])->name('listing.show')->middleware('cache.page:300');
Route::get('/listings/{slug}', [ListingController::class, 'show'])->name('listings.show')->middleware('cache.page:300');
Route::post('/listings/{listing}/review', [ListingController::class, 'storeReview'])->name('listings.review.store')->middleware(['auth', 'throttle:3,60']);
Route::post('/listings/{listing}/report', [\App\Http\Controllers\ReportController::class, 'store'])->name('listings.report')->middleware(['auth', 'throttle:5,10']);
Route::post('/users/{user}/block', [\App\Http\Controllers\UserBlockController::class, 'store'])->middleware(['auth', 'throttle:20,1'])->name('users.block');
Route::delete('/users/{user}/unblock', [\App\Http\Controllers\UserBlockController::class, 'destroy'])->middleware(['auth'])->name('users.unblock');

Route::delete('/listings/saved-searches/{id}', [\App\Http\Controllers\Frontend\UserActionController::class, 'deleteSavedSearch'])->name('listings.saved-search.delete')->middleware(['auth']);
Route::post('/listings/{listing}/price-alert', [\App\Http\Controllers\Frontend\UserActionController::class, 'togglePriceAlert'])->name('listings.price-alert')->middleware(['auth', 'throttle:20,60']);
Route::post('/listings/save-search', [\App\Http\Controllers\Frontend\UserActionController::class, 'saveSearch'])->name('listings.save-search')->middleware(['auth', 'throttle:10,60']);
Route::post('/newsletter/subscribe', [\App\Http\Controllers\Frontend\UserActionController::class, 'newsletterSubscribe'])->name('newsletter.subscribe')->middleware('throttle:3,60');
Route::post('/store/{store}/review', [\App\Http\Controllers\Frontend\StoreController::class, 'storeReview'])->name('store.review.store')->middleware(['auth', 'throttle:3,60']);
Route::get('/brand', [ListingController::class, 'brands'])->name('brands.index')->middleware('cache.page:600');
Route::get('/brands', fn () => redirect('/brand'));
Route::get('/brand/{slug}', [ListingController::class, 'brand'])->name('brand.show')->middleware('cache.page:300');

Route::get('/deals', [\App\Http\Controllers\Frontend\DealsController::class, 'index'])->name('deals.index')->middleware('cache.page:300');
Route::get('/categories', [\App\Http\Controllers\Frontend\CategoryPageController::class, 'index'])->name('categories.index')->middleware('cache.page:600');
Route::get('/locations', [\App\Http\Controllers\Frontend\LocationPageController::class, 'index'])->name('locations.index')->middleware('cache.page:600');

Route::get('/blog', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('blog.index')->middleware('cache.page:300');
Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blog.show')->middleware('cache.page:600');

Route::redirect('/about', '/about-us', 301);
Route::redirect('/contact', '/contact-us', 301);
Route::redirect('/classifieds', '/classified', 301);

Route::middleware('cache.page:86400')->group(function () {
    Route::view('/how-it-works', 'pages.how-it-works')->name('pages.how-it-works');
    Route::view('/about-us', 'pages.about')->name('pages.about');
    Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
    Route::view('/privacy-policy', 'pages.privacy')->name('pages.privacy');
    Route::view('/cookie-policy', 'pages.cookies')->name('pages.cookies');
    Route::view('/help-center', 'pages.help-center')->name('pages.help-center');
    Route::view('/how-to-buy', 'pages.how-to-buy')->name('pages.how-to-buy');
    Route::view('/how-to-sell', 'pages.how-to-sell')->name('pages.how-to-sell');
    Route::view('/safety-tips', 'pages.safety-tips')->name('pages.safety-tips');
    Route::view('/faq', 'pages.faq')->name('pages.faq');
});
Route::view('/contact-us', 'pages.contact')->name('pages.contact');
Route::get('/market-prices', [WholesalePriceController::class, 'index'])->name('market-prices');
Route::redirect('/government-services', '/public-services', 301);
Route::redirect('/government-services/{slug}', '/public-services/{slug}', 301);
Route::get('/public-services', [\App\Http\Controllers\Frontend\GovernmentServiceController::class, 'index'])->name('gov-services.index')->middleware('cache.page:1800');
Route::get('/public-services/{slug}', [\App\Http\Controllers\Frontend\GovernmentServiceController::class, 'show'])->name('gov-services.show')->middleware('cache.page:1800');
Route::get('/services', [\App\Http\Controllers\Frontend\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [\App\Http\Controllers\Frontend\ServiceController::class, 'show'])->name('services.show');
Route::post('/contact-us', [\App\Http\Controllers\Frontend\StaticPageController::class, 'contactSubmit'])->middleware('throttle:5,10')->name('pages.contact.submit');

Route::get('/explore', [\App\Http\Controllers\Frontend\ExploreController::class, 'index'])->name('pages.explore.index')->middleware('cache.page:1800');
Route::get('/explore/{slug}', [\App\Http\Controllers\Frontend\ExploreController::class, 'show'])->name('pages.explore.show')->middleware('cache.page:1800');

Route::get('/towns', [\App\Http\Controllers\Frontend\TownController::class, 'index'])->name('towns.index')->middleware('cache.page:600');
Route::get('/town', fn () => redirect('/towns'));
Route::get('/town/{slug}', [\App\Http\Controllers\Frontend\TownController::class, 'show'])->name('town.show');

Route::get('/events', [\App\Http\Controllers\Frontend\EventController::class, 'index'])->name('events.index')->middleware('cache.page:300');
Route::get('/events/calendar/{year}/{month}', [\App\Http\Controllers\Frontend\EventController::class, 'monthly'])->name('events.monthly')->middleware('cache.page:600');
Route::get('/events/{slug}', [\App\Http\Controllers\Frontend\EventController::class, 'show'])->name('events.show')->middleware('cache.page:600');

Route::view('/saved', 'frontend.saved')->name('saved');

// Recently viewed listings (localStorage IDs → listing data)
Route::get('/api/listings/recently-viewed', function (\Illuminate\Http\Request $request) {
    $raw = $request->query('ids', '');
    $ids = array_filter(array_map('intval', explode(',', $raw)));
    $ids = array_slice(array_unique($ids), 0, 10);
    if (empty($ids)) return response()->json([]);

    $listings = \App\Models\Listing::with(['images', 'category'])
        ->published()
        ->whereIn('id', $ids)
        ->get();

    // Preserve client-supplied order
    $indexed = $listings->keyBy('id');
    $ordered = collect($ids)->map(fn($id) => $indexed->get($id))->filter();

    return response()->json($ordered->map(function ($l) {
        $img = optional($l->images->first())->path ?? $l->image ?? null;
        return [
            'id'       => $l->id,
            'title'    => \Illuminate\Support\Str::limit($l->title, 52),
            'slug'     => $l->slug,
            'price'    => ($l->price ?? 0) > 0 ? 'LKR ' . number_format($l->price) : 'Price on Request',
            'location' => $l->location ?? 'Kegalle',
            'category' => optional($l->category)->name,
            'image'    => $img ? asset('storage/' . ltrim($img, '/')) : null,
        ];
    }));
})->middleware('throttle:60,1');

Route::get('/api/listings/saved', function (\Illuminate\Http\Request $request) {
    $raw = $request->query('ids', '');
    $ids = array_filter(array_map('intval', explode(',', $raw)));
    $ids = array_slice(array_unique($ids), 0, 50);
    if (empty($ids)) return response()->json([]);

    $listings = \App\Models\Listing::with(['images', 'category', 'locationModel'])
        ->published()
        ->whereIn('id', $ids)
        ->get();

    $catIconMap = [
        'vehicles' => '🚗', 'cars' => '🚗', 'motors' => '🚗',
        'electronics' => '📱', 'phones' => '📱', 'mobile' => '📱',
        'property' => '🏠', 'real estate' => '🏠', 'houses' => '🏠',
        'fashion' => '👗', 'clothing' => '👗', 'clothes' => '👗',
        'furniture' => '🪑', 'home & garden' => '🪑',
        'jobs' => '💼', 'services' => '💼',
        'sports' => '⚽', 'fitness' => '⚽',
        'pets' => '🐾', 'animals' => '🐾',
        'books' => '📚', 'education' => '📚',
        'food' => '🍎', 'grocery' => '🍎',
    ];

    $result = $listings->map(function ($l) use ($catIconMap) {
        $firstImage = optional($l->images->first())->path ?? null;
        $catName = strtolower(optional($l->category)->name ?? '');
        return [
            'id' => $l->id,
            'title' => $l->title,
            'slug' => $l->slug,
            'price' => $l->price ?? 0,
            'image' => $firstImage ? asset('storage/' . ltrim($firstImage, '/')) : null,
            'category' => optional($l->category)->name,
            'category_icon' => $catIconMap[$catName] ?? '📦',
            'location' => optional($l->locationModel)->name ?? $l->location ?? 'Kegalle',
            'time_ago' => $l->created_at ? $l->created_at->diffForHumans() : 'Recently',
            'ad_type' => $l->ad_type ?? $l->type ?? 'sale',
        ];
    });

    return response()->json($result->values());
})->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin'])->middleware('throttle:10,1')->name('login.submit');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'doRegister'])->middleware('throttle:5,1')->name('register.submit');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'doResetPassword'])->name('password.update')->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/email/verify-notice', [AuthController::class, 'verifyNotice'])->name('verification.notice');
Route::middleware('auth')->group(function () {
    Route::get('/email/resend', fn () => redirect()->route('verification.notice'));
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Chat Polling API (real-time without WebSockets)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api/chat')->group(function () {
    Route::get('unread',                [\App\Http\Controllers\Api\ChatPollController::class, 'unreadCount']);
    Route::get('{thread}/messages',     [\App\Http\Controllers\Api\ChatPollController::class, 'messages'])->middleware('throttle:120,1');
    Route::post('{thread}/send',        [\App\Http\Controllers\Api\ChatPollController::class, 'send'])->middleware('throttle:60,1');
});

/*
|--------------------------------------------------------------------------
| Web Push Notification API
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api/push')->group(function () {
    Route::post('subscribe',   [\App\Http\Controllers\Api\PushSubscriptionController::class, 'subscribe']);
    Route::post('unsubscribe', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'unsubscribe']);
    Route::get('latest',       [\App\Http\Controllers\Api\PushSubscriptionController::class, 'latest']);
});

Route::post('/dashboard/listings/ai-assist', [\App\Http\Controllers\Dashboard\AiAssistController::class, 'assist'])
    ->middleware(['auth', 'account.active', 'verified.custom', 'throttle:10,1'])
    ->name('listings.ai-assist');

Route::post('/dashboard/listings/ai-suggest-images', [\App\Http\Controllers\Admin\AdminAiController::class, 'suggestFromImages'])
    ->middleware(['auth', 'account.active', 'verified.custom', 'throttle:10,1'])
    ->name('dashboard.listings.ai-suggest-images');


// Offers
Route::middleware('auth')->group(function () {
    Route::post('/listings/{listing}/offer',    [\App\Http\Controllers\OfferController::class, 'store'])->middleware('throttle:10,5')->name('offers.store');
    Route::patch('/offers/{offer}',             [\App\Http\Controllers\OfferController::class, 'update'])->name('offers.update');
    Route::get('/dashboard/offers',             [\App\Http\Controllers\OfferController::class, 'myOffers'])->name('offers.my');
    Route::get('/dashboard/offers/received',    [\App\Http\Controllers\OfferController::class, 'receivedOffers'])->name('offers.received');
    Route::get('/dashboard/offers/{offer}',     function (\App\Models\Offer $offer) {
        $user = auth()->user();
        if ($offer->listing && $offer->listing->user_id === $user->id) {
            return redirect()->route('offers.received');
        }
        return redirect()->route('offers.my');
    })->name('offers.show');
});

// User notifications
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/notifications',                  [\App\Http\Controllers\Dashboard\UserNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/dashboard/notifications/{notification}/read', [\App\Http\Controllers\Dashboard\UserNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/dashboard/notifications/read-all',        [\App\Http\Controllers\Dashboard\UserNotificationController::class, 'markAllRead'])->name('notifications.readAll');
});


/*
|--------------------------------------------------------------------------
| Phone OTP — Hutch SMS Gateway
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/phone/verify',     [\App\Http\Controllers\Auth\PhoneOtpController::class, 'showVerify'])->name('phone.verify');
    Route::post('/phone/send-otp',  [\App\Http\Controllers\Auth\PhoneOtpController::class, 'sendOtp'])->middleware('throttle:10,1')->name('phone.send-otp');
    Route::post('/phone/verify-otp',[\App\Http\Controllers\Auth\PhoneOtpController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('phone.verify-otp');
});
// Login by phone OTP (unauthenticated)
Route::post('/phone/login-otp',        [\App\Http\Controllers\Auth\PhoneOtpController::class, 'sendLoginOtp'])->middleware(['guest','throttle:5,10'])->name('phone.login-otp');
Route::post('/phone/login-otp/verify', [\App\Http\Controllers\Auth\PhoneOtpController::class, 'verifyLoginOtp'])->middleware(['guest','throttle:10,1'])->name('phone.login-otp.verify');

Route::middleware('auth')->group(function () {
    Route::view('/account-pending', 'auth.account-pending')->name('account.pending');
    Route::view('/account-suspended', 'auth.account-suspended')->name('account.suspended');
});

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');
Route::match(['get','post'], '/g-return', [SocialLoginController::class, 'callback'])->defaults('provider', 'google');
// Google Identity Services token verification (popup flow — no redirect URI needed)
Route::post('/auth/google/token', [SocialLoginController::class, 'googleToken'])->name('google.token');
// Facebook JS SDK token verification (popup flow — no redirect URI needed)
Route::post('/auth/facebook/token', [SocialLoginController::class, 'facebookToken'])->name('facebook.token');

/*
|--------------------------------------------------------------------------
| User + Store Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified.custom', 'account.active'])->prefix('dashboard')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'edit'])->name('dashboard.profile');
    Route::put('/profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('dashboard.profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Dashboard\ProfileController::class, 'updatePassword'])->name('dashboard.profile.password');

    Route::get('listings/ai-diag', [\App\Http\Controllers\Dashboard\AiAssistController::class, 'diag'])->name('listings.ai-diag');
    Route::resource('listings', DashListing::class)->names('dashboard.listings');
    Route::patch('listings/{listing}/sold', [DashListing::class, 'markSold'])->name('dashboard.listings.sold');
    Route::patch('listings/{listing}/renew', [DashListing::class, 'renew'])->name('dashboard.listings.renew');
    Route::patch('listings/{listing}/bump', [DashListing::class, 'bump'])->name('dashboard.listings.bump');
    Route::patch('listings/{listing}/stock', [DashListing::class, 'updateStock'])->name('dashboard.listings.stock');

    Route::get('membership', [MembershipUpgradeController::class, 'index'])->name('dashboard.membership');
    Route::get('membership/upgrade/{plan}', [MembershipUpgradeController::class, 'show'])->name('dashboard.membership.upgrade');
    Route::post('membership/upgrade/{plan}', [MembershipUpgradeController::class, 'submit'])->name('dashboard.membership.upgrade.submit')->middleware('throttle:5,60');
    Route::get('payments', [MembershipUpgradeController::class, 'payments'])->name('dashboard.payments');
    Route::get('transactions', [\App\Http\Controllers\Dashboard\TransactionHistoryController::class, 'index'])->name('dashboard.transactions');
    Route::get('chat', [\App\Http\Controllers\Dashboard\ChatController::class, 'index'])->name('dashboard.chat');
    Route::get('chat/{thread}', [\App\Http\Controllers\Dashboard\ChatController::class, 'show'])->name('dashboard.chat.show');
    Route::post('chat', [\App\Http\Controllers\Dashboard\ChatController::class, 'store'])->name('dashboard.chat.store');
    Route::post('chat/{thread}/reply', [\App\Http\Controllers\Dashboard\ChatController::class, 'reply'])->name('dashboard.chat.reply')->middleware('throttle:60,1');
    Route::get('reviews', function () {
        $user = auth()->user();
        $storeIds = $user->stores()->pluck('id');
        $reviews = \App\Models\Review::whereIn('store_id', $storeIds)
            ->orWhere(function($q) use ($storeIds) {
                $q->whereIn('listing_id', \App\Models\Listing::whereIn('store_id', $storeIds)->pluck('id'));
            })
            ->with(['user', 'store', 'listing'])
            ->latest()
            ->paginate(20);
        $avgRating = \App\Models\Review::whereIn('store_id', $storeIds)->where('status', 'approved')->avg('rating');
        $totalReviews = \App\Models\Review::whereIn('store_id', $storeIds)->where('status', 'approved')->count();
        return view('dashboard.reviews.index', compact('reviews', 'avgRating', 'totalReviews'));
    })->name('dashboard.reviews');
    Route::get('favorites', [\App\Http\Controllers\Dashboard\FavoriteController::class, 'index'])->name('dashboard.favorites');
    Route::post('favorites/{listing}/toggle', [\App\Http\Controllers\Dashboard\FavoriteController::class, 'toggle'])->name('dashboard.favorites.toggle');

    Route::get('deals', [\App\Http\Controllers\Dashboard\DealController::class, 'index'])->name('dashboard.deals.index');
    Route::get('deals/create', [\App\Http\Controllers\Dashboard\DealController::class, 'create'])->name('dashboard.deals.create');
    Route::get('deals/create-with-product', [\App\Http\Controllers\Dashboard\DealController::class, 'createWithProduct'])->name('dashboard.deals.createWithProduct');
    Route::post('deals/store-with-product', [\App\Http\Controllers\Dashboard\DealController::class, 'storeWithProduct'])->name('dashboard.deals.storeWithProduct');
    Route::post('deals', [\App\Http\Controllers\Dashboard\DealController::class, 'store'])->name('dashboard.deals.store');
    Route::get('deals/{deal}/edit', [\App\Http\Controllers\Dashboard\DealController::class, 'edit'])->name('dashboard.deals.edit');
    Route::put('deals/{deal}', [\App\Http\Controllers\Dashboard\DealController::class, 'update'])->name('dashboard.deals.update');
    Route::delete('deals/{deal}', [\App\Http\Controllers\Dashboard\DealController::class, 'destroy'])->name('dashboard.deals.destroy');

    Route::resource('services', \App\Http\Controllers\Dashboard\ServiceController::class)->names('dashboard.services');

    Route::resource('stores', DashStore::class)->names('dashboard.stores');
    Route::get('stores/{store}/analytics', [DashStore::class, 'analytics'])->name('dashboard.stores.analytics');
    Route::get('stores/{store}/reviews', [DashStore::class, 'reviews'])->name('dashboard.stores.reviews');
    Route::post('stores/{store}/reviews/{review}/reply', [DashStore::class, 'replyReview'])->name('dashboard.stores.reviews.reply');
    Route::resource('stores.products', StoreProductController::class)->names('dashboard.stores.products');
    // TODO: Move to Dashboard\StoreImportController
    Route::get('stores/{store}/import', function(\App\Models\Store $store) {
        abort_if(auth()->id() !== $store->user_id, 403);
        return view('dashboard.stores.import', compact('store'));
    })->name('dashboard.stores.import');
    // TODO: Move to Dashboard\StoreImportController
    Route::post('stores/{store}/import', function(\Illuminate\Http\Request $request, \App\Models\Store $store) {
        abort_if(auth()->id() !== $store->user_id, 403);
        $request->validate(['csv' => 'required|file|mimes:csv,txt|max:2048']);
        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);
        $now = now();
        $batch = []; $imported = 0; $errors = [];
        while (($row = fgetcsv($handle)) !== false) {
            if ($imported >= 500) { $errors[] = 'Row limit reached (500 max).'; break; }
            if (count($row) < 2) continue;
            $data = array_combine($headers, array_pad($row, count($headers), ''));
            $title = strip_tags(trim($data['title'] ?? $data['name'] ?? ''));
            if (!$title || mb_strlen($title) > 190) { $errors[] = "Row skipped: no title or title too long"; continue; }
            $batch[] = [
                'store_id'    => $store->id,
                'user_id'     => $store->user_id,
                'title'       => $title,
                'slug'        => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
                'description' => strip_tags(trim($data['description'] ?? $data['desc'] ?? '')),
                'price'       => max(0, (float) preg_replace('/[^0-9.]/', '', $data['price'] ?? '0')),
                'location'    => strip_tags(trim($data['location'] ?? 'Kegalle')),
                'condition'   => strip_tags(trim($data['condition'] ?? 'Brand New')),
                'type'        => 'product',
                'status'      => 'pending',
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $imported++;
            // Flush every 50 rows to avoid giant single query
            if (count($batch) >= 50) {
                try { \Illuminate\Support\Facades\DB::table('listings')->insert($batch); }
                catch(\Throwable $e) { $errors[] = 'Row import failed — check data format.'; $imported -= count($batch); }
                $batch = [];
            }
        }
        fclose($handle);
        if ($batch) {
            try { \Illuminate\Support\Facades\DB::table('listings')->insert($batch); }
            catch(\Throwable $e) { $errors[] = 'Row import failed — check data format.'; $imported -= count($batch); }
        }
        // Bust per-user dashboard cache so new listings appear immediately
        $uid = $store->user_id;
        cache()->forget("dashboard_data_{$uid}");
        cache()->forget("sidebar_stores_{$uid}");
        $msg = "Imported $imported listings.";
        if ($errors) $msg .= ' Errors: ' . implode('; ', array_slice($errors, 0, 5));
        return back()->with('success', $msg);
    })->name('dashboard.stores.import.post')->middleware('throttle:5,60');

    // AI helpers accessible to all authenticated dashboard users
    Route::post('ai/suggest-listing', [\App\Http\Controllers\Admin\AdminAiController::class, 'suggestFromImages'])->name('dashboard.ai.suggest-listing')->middleware('throttle:10,1');
    Route::post('ai/translate-text', [\App\Http\Controllers\Admin\AdminAiController::class, 'translateText'])->name('dashboard.ai.translate-text')->middleware('throttle:30,1');

    // Contact Administrator
    Route::get('contact-admin', function () {
        return view('dashboard.contact-admin');
    })->name('dashboard.contact-admin');

    // TODO: Move to Dashboard\StoreImportController
    Route::post('contact-admin', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:3000',
        ]);
        $user = auth()->user();
        $body = '[Seller Dashboard — ' . $data['subject'] . "]\n\n"
            . "From: {$user->name} ({$user->email})\n"
            . "User ID: #{$user->id}\n\n"
            . $data['message'];
        try {
            \Illuminate\Support\Facades\Mail::to('support@kegalle.com')
                ->queue(new \App\Mail\ContactMessageMail($user->name, $user->email, $body));
        } catch (\Throwable) {}
        return back()->with('success', 'Your message has been sent to the administrator. We will reply to ' . $user->email . ' shortly.');
    })->name('dashboard.contact-admin.send')->middleware('throttle:5,10');
});

// WA contact redirect — hides phone number from HTML source
Route::get('/c/wa/{listing}', function (\App\Models\Listing $listing) {
    $number = preg_replace('/[^0-9]/', '', optional($listing->store)->whatsapp ?? optional($listing->store)->phone ?? $listing->poster_whatsapp ?? $listing->poster_phone ?? optional($listing->user)->phone ?? '');
    if (!$number) abort(404);
    $msg = urlencode('Hi, I\'m interested in: '.$listing->title.' — '.url('/listings/'.$listing->slug));
    return redirect("https://wa.me/{$number}?text={$msg}");
})->name('listing.wa.redirect');

// Guest enquiry (no login required)
Route::post('/listings/{listing}/enquire', [\App\Http\Controllers\Frontend\GuestEnquiryController::class, 'store'])->middleware('throttle:3,10')->name('listing.enquire');

// Redirect unauthenticated /admin to home (don't confirm admin route to scanners)
Route::get('/admin', fn() => redirect('/'))->middleware('guest');
Route::get('/admin/login', fn() => redirect('/login'));

/*
|--------------------------------------------------------------------------
| Super Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'account.active', 'is_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/dashboard', fn() => redirect('/admin'))->name('admin.dashboard');

    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/export-csv', [UserManagementController::class, 'exportCsv'])->name('admin.users.export-csv');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{user}/make-admin', [UserManagementController::class, 'makeAdmin'])->name('admin.users.makeAdmin');
    Route::post('/users/{user}/make-super-admin', [UserManagementController::class, 'makeSuperAdmin'])->name('admin.users.makeSuperAdmin');
    Route::post('/users/{user}/toggle-verified', [UserManagementController::class, 'toggleVerified'])->name('admin.users.toggleVerified');
    Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');
    Route::post('/users/{user}/toggle-multiple-stores', [UserManagementController::class, 'toggleMultipleStores'])->name('admin.users.toggleMultipleStores');
    Route::post('/users/{user}/store-limit', [UserManagementController::class, 'setStoreLimit'])->name('admin.users.storeLimit');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::post('/users/{user}/assign-role', [\App\Http\Controllers\Admin\RoleController::class, 'assignToUser'])->name('admin.users.assignRole');

    Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store');
    Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/approvals', [ModerationController::class, 'approvals'])->name('admin.approvals');
    Route::get('/inactive', [ModerationController::class, 'inactive'])->name('admin.inactive');
    Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('admin.listings.destroy');
    Route::delete('/stores/{store}', [AdminStoreController::class, 'destroy'])->name('admin.stores.destroy');
    Route::get('/stores', [AdminStoreController::class, 'index'])->name('admin.stores.index');
    Route::get('/stores/create', [AdminStoreController::class, 'create'])->name('admin.stores.create');
    Route::post('/stores', [AdminStoreController::class, 'store'])->name('admin.stores.store');
    Route::post('/stores/{store}/approve', [AdminStoreController::class, 'approve'])->name('admin.stores.approve');
    Route::post('/stores/{store}/suspend', [AdminStoreController::class, 'suspend'])->name('admin.stores.suspend');
    Route::post('/stores/{store}/feature', [AdminStoreController::class, 'feature'])->name('admin.stores.feature');
    Route::post('/stores/{store}/verify', [AdminStoreController::class, 'verify'])->name('admin.stores.verify');
    Route::get('/stores/{store}/edit', [AdminStoreController::class, 'edit'])->name('admin.stores.edit');
    Route::put('/stores/{store}', [AdminStoreController::class, 'update'])->name('admin.stores.update');
    Route::post('/stores/{store}/transfer', [AdminStoreController::class, 'transfer'])->name('admin.stores.transfer');

    Route::get('/brands', [\App\Http\Controllers\Admin\BrandManagementController::class, 'index'])->name('admin.brands.index');
    Route::get('/brands/by-category', [\App\Http\Controllers\Admin\BrandManagementController::class, 'byCategory'])->name('admin.brands.by-category');
    Route::get('/brands/create', [\App\Http\Controllers\Admin\BrandManagementController::class, 'create'])->name('admin.brands.create');
    Route::post('/brands', [\App\Http\Controllers\Admin\BrandManagementController::class, 'store'])->name('admin.brands.store');
    Route::get('/brands/{brand}/edit', [\App\Http\Controllers\Admin\BrandManagementController::class, 'edit'])->name('admin.brands.edit');
    Route::put('/brands/{brand}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'update'])->name('admin.brands.update');
    Route::post('/brands/{brand}/toggle', [\App\Http\Controllers\Admin\BrandManagementController::class, 'toggleBrand'])->name('admin.brands.toggle');
    Route::delete('/brands/{brand}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'destroy'])->name('admin.brands.destroy');
    Route::post('/brands/models', [\App\Http\Controllers\Admin\BrandManagementController::class, 'storeModel'])->name('admin.brands.models.store');
    Route::put('/brands/models/{model}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'updateModel'])->name('admin.brands.models.update');
    Route::delete('/brands/models/{model}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'destroyModel'])->name('admin.brands.models.destroy');

    Route::get('/listings', [AdminListingController::class, 'index'])->name('admin.listings.index');
    Route::get('/listings/export-csv', [AdminListingController::class, 'exportCsv'])->name('admin.listings.export-csv');
    Route::post('/listings/bulk-approve', [AdminListingController::class, 'bulkApprove'])->name('admin.listings.bulk-approve');
    Route::get('/classifieds', [AdminListingController::class, 'classifieds'])->name('admin.classifieds.index');
    Route::get('/classifieds/create', [AdminListingController::class, 'createClassified'])->name('admin.classifieds.create');
    Route::post('/classifieds', [AdminListingController::class, 'storeClassified'])->name('admin.classifieds.store');
    Route::get('/classifieds/{listing}/edit', [AdminListingController::class, 'editClassified'])->name('admin.classifieds.edit');
    Route::put('/classifieds/{listing}', [AdminListingController::class, 'updateClassified'])->name('admin.classifieds.update');
    Route::get('/listings/create', [AdminListingController::class, 'create'])->name('admin.listings.create');
    Route::post('/listings', [AdminListingController::class, 'store'])->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('admin.listings.edit');
    Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('admin.listings.update');
    Route::post('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('admin.listings.approve');
    Route::post('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('admin.listings.reject');
    Route::post('/listings/{listing}/feature', [AdminListingController::class, 'feature'])->name('admin.listings.feature');
    Route::post('/listings/{listing}/top', [AdminListingController::class, 'top'])->name('admin.listings.top');

    Route::get('/services', [\App\Http\Controllers\Admin\AdminServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/services/{service}/approve', [\App\Http\Controllers\Admin\AdminServiceController::class, 'approve'])->name('admin.services.approve');
    Route::post('/services/{service}/reject', [\App\Http\Controllers\Admin\AdminServiceController::class, 'reject'])->name('admin.services.reject');
    Route::delete('/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/categories', [CategoryManagementController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryManagementController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryManagementController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryManagementController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{category}/toggle', [CategoryManagementController::class, 'toggle'])->name('admin.categories.toggle');
    Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('admin.categories.destroy');

    Route::post('/ai/suggest-listing', [AdminAiController::class, 'suggestFromImages'])->name('admin.ai.suggest-listing');
    Route::post('/ai/translate-text', [AdminAiController::class, 'translateText'])->name('admin.ai.translate-text');

    Route::get('/listing-fields', [FieldManagementController::class, 'index'])->name('admin.listing-fields.index');
    Route::post('/listing-fields/fields', [FieldManagementController::class, 'storeField']);
    Route::put('/listing-fields/fields/{field}', [FieldManagementController::class, 'updateField']);
    Route::delete('/listing-fields/fields/{field}', [FieldManagementController::class, 'destroyField']);
    Route::post('/listing-fields/assign', [FieldManagementController::class, 'assignFields']);
    Route::get('/listing-fields/category-fields/{category}', [FieldManagementController::class, 'getCategoryFields']);
    Route::post('/listing-fields/brands', [FieldManagementController::class, 'storeBrand']);
    Route::put('/listing-fields/brands/{brand}', [FieldManagementController::class, 'updateBrand']);
    Route::delete('/listing-fields/brands/{brand}', [FieldManagementController::class, 'destroyBrand']);
    Route::post('/listing-fields/models', [FieldManagementController::class, 'storeModel']);
    Route::put('/listing-fields/models/{model}', [FieldManagementController::class, 'updateModel']);
    Route::delete('/listing-fields/models/{model}', [FieldManagementController::class, 'destroyModel']);

    Route::get('/locations', [LocationManagementController::class, 'index'])->name('admin.locations.index');
    Route::post('/locations', [LocationManagementController::class, 'store'])->name('admin.locations.store');
    Route::get('/locations/{location}/edit', [LocationManagementController::class, 'edit'])->name('admin.locations.edit');
    Route::put('/locations/{location}', [LocationManagementController::class, 'update'])->name('admin.locations.update');
    Route::post('/locations/{location}/toggle', [LocationManagementController::class, 'toggle'])->name('admin.locations.toggle');
    Route::delete('/locations/{location}', [LocationManagementController::class, 'destroy'])->name('admin.locations.destroy');

    Route::get('/posts', [\App\Http\Controllers\Admin\PostManagementController::class, 'index'])->name('admin.posts.index');
    Route::post('/posts', [\App\Http\Controllers\Admin\PostManagementController::class, 'store'])->name('admin.posts.store');
    Route::get('/posts/{post}/edit', [\App\Http\Controllers\Admin\PostManagementController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post}', [\App\Http\Controllers\Admin\PostManagementController::class, 'update'])->name('admin.posts.update');
    Route::post('/posts/{post}/toggle', [\App\Http\Controllers\Admin\PostManagementController::class, 'toggle'])->name('admin.posts.toggle');
    Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\PostManagementController::class, 'destroy'])->name('admin.posts.destroy');

    Route::get('/memberships', [MembershipController::class, 'index'])->name('admin.memberships.index');
    Route::post('/memberships', [MembershipController::class, 'store'])->name('admin.memberships.store');
    Route::get('/memberships/{membership}/edit', [MembershipController::class, 'edit'])->name('admin.memberships.edit');
    Route::put('/memberships/{membership}', [MembershipController::class, 'update'])->name('admin.memberships.update');
    Route::post('/memberships/{membership}/toggle', [MembershipController::class, 'toggle'])->name('admin.memberships.toggle');
    Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy'])->name('admin.memberships.destroy');

    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('admin.payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');
    Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('admin.payments.status');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');
    Route::get('/payments/{payment}/slip', [PaymentController::class, 'slip'])->name('admin.payments.slip');

    Route::get('/reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::patch('/reports/{report}', [\App\Http\Controllers\Admin\AdminReportController::class, 'update'])->name('admin.reports.update');

    Route::get('/deals', [\App\Http\Controllers\Admin\DealManagementController::class, 'index'])->name('admin.deals.index');
    Route::get('/deals/create', [\App\Http\Controllers\Admin\DealManagementController::class, 'create'])->name('admin.deals.create');
    Route::post('/deals', [\App\Http\Controllers\Admin\DealManagementController::class, 'store'])->name('admin.deals.store');
    Route::get('/deals/{deal}/edit', [\App\Http\Controllers\Admin\DealManagementController::class, 'edit'])->name('admin.deals.edit');
    Route::put('/deals/{deal}', [\App\Http\Controllers\Admin\DealManagementController::class, 'update'])->name('admin.deals.update');
    Route::post('/deals/{deal}/approve', [\App\Http\Controllers\Admin\DealManagementController::class, 'approve'])->name('admin.deals.approve');
    Route::post('/deals/{deal}/reject', [\App\Http\Controllers\Admin\DealManagementController::class, 'reject'])->name('admin.deals.reject');
    Route::post('/deals/{deal}/feature', [\App\Http\Controllers\Admin\DealManagementController::class, 'feature'])->name('admin.deals.feature');
    Route::post('/deals/{deal}/flash', [\App\Http\Controllers\Admin\DealManagementController::class, 'flash'])->name('admin.deals.flash');
    Route::delete('/deals/{deal}', [\App\Http\Controllers\Admin\DealManagementController::class, 'destroy'])->name('admin.deals.destroy');

    Route::get('/events', [\App\Http\Controllers\Admin\EventManagementController::class, 'index'])->name('admin.events.index');
    Route::get('/events/create', [\App\Http\Controllers\Admin\EventManagementController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [\App\Http\Controllers\Admin\EventManagementController::class, 'store'])->name('admin.events.store');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\Admin\EventManagementController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\Admin\EventManagementController::class, 'update'])->name('admin.events.update');
    Route::post('/events/{event}/approve', [\App\Http\Controllers\Admin\EventManagementController::class, 'approve'])->name('admin.events.approve');
    Route::post('/events/{event}/reject', [\App\Http\Controllers\Admin\EventManagementController::class, 'reject'])->name('admin.events.reject');
    Route::post('/events/{event}/feature', [\App\Http\Controllers\Admin\EventManagementController::class, 'feature'])->name('admin.events.feature');
    Route::delete('/events/{event}', [\App\Http\Controllers\Admin\EventManagementController::class, 'destroy'])->name('admin.events.destroy');

    Route::get('/chats', [ChatController::class, 'index'])->name('admin.chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('admin.chats.show');
    Route::post('/chats/{chat}/close', [ChatController::class, 'close'])->name('admin.chats.close');
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy'])->name('admin.chats.destroy');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/maintenance/toggle', [SettingController::class, 'toggleMaintenance'])->name('admin.maintenance.toggle');


    Route::get('/ad-banners', [\App\Http\Controllers\Admin\AdBannerController::class, 'index'])->name('admin.ad-banners.index');
    Route::get('/ad-banners/create', [\App\Http\Controllers\Admin\AdBannerController::class, 'create'])->name('admin.ad-banners.create');
    Route::post('/ad-banners', [\App\Http\Controllers\Admin\AdBannerController::class, 'store'])->name('admin.ad-banners.store');
    Route::get('/ad-banners/{adBanner}/edit', [\App\Http\Controllers\Admin\AdBannerController::class, 'edit'])->name('admin.ad-banners.edit');
    Route::put('/ad-banners/{adBanner}', [\App\Http\Controllers\Admin\AdBannerController::class, 'update'])->name('admin.ad-banners.update');
    Route::post('/ad-banners/{adBanner}/toggle', [\App\Http\Controllers\Admin\AdBannerController::class, 'toggle'])->name('admin.ad-banners.toggle');
    Route::delete('/ad-banners/{adBanner}', [\App\Http\Controllers\Admin\AdBannerController::class, 'destroy'])->name('admin.ad-banners.destroy');

    Route::get('/explore-items', [\App\Http\Controllers\Admin\ExploreItemController::class, 'index'])->name('admin.explore-items.index');
    Route::get('/explore-items/create', [\App\Http\Controllers\Admin\ExploreItemController::class, 'create'])->name('admin.explore-items.create');
    Route::post('/explore-items', [\App\Http\Controllers\Admin\ExploreItemController::class, 'store'])->name('admin.explore-items.store');
    Route::get('/explore-items/{exploreItem}/edit', [\App\Http\Controllers\Admin\ExploreItemController::class, 'edit'])->name('admin.explore-items.edit');
    Route::put('/explore-items/{exploreItem}', [\App\Http\Controllers\Admin\ExploreItemController::class, 'update'])->name('admin.explore-items.update');
    Route::post('/explore-items/{exploreItem}/toggle', [\App\Http\Controllers\Admin\ExploreItemController::class, 'toggle'])->name('admin.explore-items.toggle');
    Route::delete('/explore-items/{exploreItem}', [\App\Http\Controllers\Admin\ExploreItemController::class, 'destroy'])->name('admin.explore-items.destroy');

    Route::get('/explore-items/{exploreItem}/items', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'index'])->name('admin.explore-sub-items.index');
    Route::get('/explore-items/{exploreItem}/items/create', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'create'])->name('admin.explore-sub-items.create');
    Route::post('/explore-items/{exploreItem}/items', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'store'])->name('admin.explore-sub-items.store');
    Route::get('/explore-items/{exploreItem}/items/{item}/edit', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'edit'])->name('admin.explore-sub-items.edit');
    Route::put('/explore-items/{exploreItem}/items/{item}', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'update'])->name('admin.explore-sub-items.update');
    Route::post('/explore-items/{exploreItem}/items/{item}/toggle', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'toggle'])->name('admin.explore-sub-items.toggle');
    Route::delete('/explore-items/{exploreItem}/items/{item}', [\App\Http\Controllers\Admin\ExploreSubItemController::class, 'destroy'])->name('admin.explore-sub-items.destroy');

    Route::get('/government-services', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'index'])->name('admin.gov-services.index');
    Route::get('/government-services/create', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'create'])->name('admin.gov-services.create');
    Route::post('/government-services', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'store'])->name('admin.gov-services.store');
    Route::get('/government-services/{governmentService}/edit', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'edit'])->name('admin.gov-services.edit');
    Route::put('/government-services/{governmentService}', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'update'])->name('admin.gov-services.update');
    Route::post('/government-services/{governmentService}/toggle', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'toggle'])->name('admin.gov-services.toggle');
    Route::delete('/government-services/{governmentService}', [\App\Http\Controllers\Admin\GovernmentServiceController::class, 'destroy'])->name('admin.gov-services.destroy');

    Route::get('/government-services/{governmentService}/items', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'index'])->name('admin.gov-items.index');
    Route::get('/government-services/{governmentService}/items/create', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'create'])->name('admin.gov-items.create');
    Route::post('/government-services/{governmentService}/items', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'store'])->name('admin.gov-items.store');
    Route::get('/government-services/{governmentService}/items/{item}/edit', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'edit'])->name('admin.gov-items.edit');
    Route::put('/government-services/{governmentService}/items/{item}', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'update'])->name('admin.gov-items.update');
    Route::post('/government-services/{governmentService}/items/{item}/toggle', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'toggle'])->name('admin.gov-items.toggle');
    Route::delete('/government-services/{governmentService}/items/{item}', [\App\Http\Controllers\Admin\GovernmentServiceItemController::class, 'destroy'])->name('admin.gov-items.destroy');

    Route::get('/hero-slides', [\App\Http\Controllers\Admin\HeroSlideController::class, 'index'])->name('admin.hero-slides.index');
    Route::post('/hero-slides', [\App\Http\Controllers\Admin\HeroSlideController::class, 'store'])->name('admin.hero-slides.store');
    Route::put('/hero-slides/{heroSlide}', [\App\Http\Controllers\Admin\HeroSlideController::class, 'update'])->name('admin.hero-slides.update');
    Route::post('/hero-slides/{heroSlide}/toggle', [\App\Http\Controllers\Admin\HeroSlideController::class, 'toggle'])->name('admin.hero-slides.toggle');
    Route::delete('/hero-slides/{heroSlide}', [\App\Http\Controllers\Admin\HeroSlideController::class, 'destroy'])->name('admin.hero-slides.destroy');

    Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markRead'])->name('admin.notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.readAll');
});

Route::middleware('throttle:60,1')->group(function () {

Route::get('/api/search-suggestions', function (\Illuminate\Http\Request $request) {
    $q = trim($request->input('q', ''));
    if (strlen($q) < 2) return response()->json([]);
    $safe = str_replace(['%', '_', '+', '-', '>', '<', '(', ')', '~', '*', '"', '@'], ' ', $q);
    $safe = trim(preg_replace('/\s+/', ' ', $safe));
    $listingQuery = \App\Models\Listing::published()->with('category')->take(6);
    $escaped = str_replace(['%', '_'], ['\%', '\_'], $q);
    $listingQuery->where('title', 'like', '%' . $escaped . '%')->orderBy('is_featured','desc')->latest();
    $listings = $listingQuery->with('images')->get(['id', 'title', 'slug', 'price', 'location', 'category_id', 'is_featured']);
    return response()->json($listings->map(fn ($l) => [
        'title'    => \Illuminate\Support\Str::limit($l->title, 52),
        'url'      => '/listings/' . $l->slug,
        'category' => optional($l->category)->name,
        'price'    => ($l->price ?? 0) > 0 ? 'LKR ' . number_format($l->price) : null,
        'location' => $l->location,
        'featured' => (bool) $l->is_featured,
        'image'    => optional($l->images->first())->path
                      ? asset('storage/' . ltrim($l->images->first()->path, '/'))
                      : null,
    ]));
});

// ── Category fields API ──────────────────────────────────────
Route::get('/api/category-fields/{categoryId}', function ($categoryId) {
    // Load the selected category and walk up the tree to find the nearest ancestor with assigned fields
    $category = \App\Models\Category::with([
        'customFields' => fn ($q) => $q->orderByPivot('sort_order'),
        'parent.customFields' => fn ($q) => $q->orderByPivot('sort_order'),
        'parent.parent.customFields' => fn ($q) => $q->orderByPivot('sort_order'),
    ])->find($categoryId);
    if (!$category) return response()->json(['fields' => []]);

    // Use fields from the most specific level that has assignments
    $resolvedCategory = $category;
    if ($category->customFields->isEmpty() && $category->parent) {
        $resolvedCategory = $category->parent;
        if ($resolvedCategory->customFields->isEmpty() && $resolvedCategory->parent) {
            $resolvedCategory = $resolvedCategory->parent;
        }
    }

    $parentSlug = optional($category->parent)->slug ?? $category->slug;
    $brandGroupMap = config('brand_groups');
    // Brand group and auto-title: check slug chain (selected → parent → grandparent)
    $slugChain = array_filter([
        $category->slug,
        optional($category->parent)->slug,
        optional(optional($category->parent)->parent)->slug,
    ]);
    $brandGroup = null;
    foreach ($slugChain as $slug) {
        if (isset($brandGroupMap[$slug])) { $brandGroup = $brandGroupMap[$slug]; break; }
    }
    $autoTitle = in_array($brandGroup, ['mobile', 'computer', 'tv', 'camera', 'vehicle', 'vehicle_bike', 'vehicle_boat']);
    $topCategoryId = $category->id;
    return response()->json([
        'fields' => $resolvedCategory->customFields->map(fn ($f) => [
            'id' => $f->id, 'label' => $f->label, 'name' => $f->name, 'type' => $f->type,
            'options' => $f->options, 'placeholder' => $f->placeholder,
            'unit' => $f->unit ?? null,
            'multi' => (bool) ($f->multi ?? false),
            'full_width' => (bool) ($f->full_width ?? false),
            'is_required' => (bool) $f->pivot->is_required,
        ]),
        'brand_group'     => $brandGroup,
        'auto_title'      => $autoTitle,
        'category_name'   => $category->name,
        'parent_slug'     => $parentSlug,
        'top_category_id' => $topCategoryId,
    ]);
});

Route::get('/api/brands/by-category/{categoryId}', function ($categoryId) {
    $allIds = [(int) $categoryId];

    // Expand downward — all descendants
    $toExpand = $allIds;
    while (!empty($toExpand)) {
        $childIds = \App\Models\Category::whereIn('parent_id', $toExpand)->pluck('id')->toArray();
        $newIds = array_diff($childIds, $allIds);
        if (empty($newIds)) break;
        $allIds = array_merge($allIds, $newIds);
        $toExpand = $newIds;
    }

    // Expand upward — all ancestors (in-memory traversal, single query)
    $allCategories = \App\Models\Category::select('id', 'parent_id', 'name', 'slug')->get()->keyBy('id');
    $node = $allCategories->get((int) $categoryId);
    while ($node && $node->parent_id) {
        if (!in_array($node->parent_id, $allIds)) {
            $allIds[] = $node->parent_id;
        }
        $node = $allCategories->get($node->parent_id);
    }

    $brands = \App\Models\Brand::active()
        ->where('name', '!=', 'Other')
        ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $allIds))
        ->orderBy('name')
        ->get(['id', 'name']);
    if ($brands->isEmpty()) {
        $brands = \App\Models\Brand::active()->where('name', '!=', 'Other')->orderBy('name')->limit(50)->get(['id', 'name']);
    }
    // Always append "Other" at the end
    $other = \App\Models\Brand::where('name', 'Other')->first(['id', 'name']);
    if ($other) $brands->push($other);
    return response()->json($brands);
});

Route::get('/api/brands/{group?}', function ($group = null) {
    $query = \App\Models\Brand::active()->orderBy('sort_order')->orderBy('name');
    if ($group) {
        $query->where('category_group', $group);
    }
    return response()->json($query->get(['id', 'name']));
});

Route::get('/api/brand-models/{brandId}', function ($brandId) {
    return response()->json(\App\Models\BrandModel::where('brand_id', $brandId)->where('is_active', true)->orderBy('name')->get(['id', 'name']));
});

Route::get('/api/subcategories/{parentId}', function ($parentId) {
    return response()->json(\App\Models\Category::where('parent_id', $parentId)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'icon']));
});

}); // end throttle:60,1 group


Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/fix-views', function (\Illuminate\Http\Request $req) {
        $expected = env('FIX_VIEWS_TOKEN', '');
        if ($req->query('token') !== $expected) abort(403);
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:cache');
        if (function_exists('opcache_reset')) opcache_reset();
        $pcDir = storage_path('framework/pagecache');
        $pcDel = 0;
        foreach (glob($pcDir . '/*.html') ?: [] as $f) { @unlink($f); $pcDel++; }
        return response('Views cleared and cached. App cache cleared. OPcache reset. Pagecache: '.$pcDel.' files cleared.')->header('Cache-Control','no-store');
    })->middleware('throttle:5,1');

    Route::get('/admin/test-emails', function (\Illuminate\Http\Request $req) {
        $expected = env('TEST_EMAIL_TOKEN', '');
        if ($expected === '' || $req->query('token') !== $expected) abort(403);
        $to = auth()->user()->email;
        \Illuminate\Support\Facades\Artisan::call('test:emails', ['--to' => $to]);
        return response("<pre style='padding:20px'>".htmlspecialchars(\Illuminate\Support\Facades\Artisan::output())."</pre>")->header('Cache-Control','no-store');
    })->middleware('throttle:3,1');
});

// Wholesale Prices — admin
Route::prefix('admin')->middleware(['auth', 'account.active', 'is_admin'])->group(function () {
    Route::get('/wholesale-prices', [AdminWholesalePriceController::class, 'index'])->name('admin.wholesale-prices.index');
    Route::post('/wholesale-prices', [AdminWholesalePriceController::class, 'store'])->name('admin.wholesale-prices.store');
    Route::delete('/wholesale-prices/{date}', [AdminWholesalePriceController::class, 'destroy'])->name('admin.wholesale-prices.destroy');
});

Route::fallback(fn () => abort(404));
