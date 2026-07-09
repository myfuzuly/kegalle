<?php

use App\Http\Controllers\Admin\AdminController;
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
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Dashboard\ListingController as DashListing;
use App\Http\Controllers\Dashboard\StoreController as DashStore;
use App\Http\Controllers\Dashboard\StoreProductController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ListingController;
use App\Http\Controllers\Frontend\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-listings.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'listings'])->name('sitemap.listings');
Route::get('/sitemap-stores.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'stores'])->name('sitemap.stores');
Route::get('/sitemap-events.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'events'])->name('sitemap.events');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemap-towns.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'towns'])->name('sitemap.towns');

Route::get('/ads/{adBanner}/click', function (\App\Models\AdBanner $adBanner) {
    $adBanner->increment('clicks');

    return redirect()->away($adBanner->link_url ?: url('/'));
})->name('ads.click');

Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('stores.show');

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/classified', [ListingController::class, 'index'])->defaults('type', 'classified')->name('classified.index');
Route::get('/listing/{slug}', [ListingController::class, 'show'])->name('listing.show');
Route::get('/listings/{slug}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/listings/{listing}/review', [ListingController::class, 'storeReview'])->name('listings.review.store')->middleware('auth');
Route::post('/store/{store}/review', [\App\Http\Controllers\Frontend\StoreController::class, 'storeReview'])->name('store.review.store')->middleware('auth');
Route::get('/brand', [ListingController::class, 'brands'])->name('brands.index');
Route::get('/brands', fn () => redirect('/brand'));
Route::get('/brand/{slug}', [ListingController::class, 'brand'])->name('brand.show');

Route::get('/deals', [\App\Http\Controllers\Frontend\DealsController::class, 'index'])->name('deals.index');
Route::get('/categories', [\App\Http\Controllers\Frontend\CategoryPageController::class, 'index'])->name('categories.index');
Route::get('/locations', [\App\Http\Controllers\Frontend\LocationPageController::class, 'index'])->name('locations.index');

Route::get('/blog', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blog.show');

Route::view('/about-us', 'pages.about')->name('pages.about');
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
Route::view('/privacy-policy', 'pages.privacy')->name('pages.privacy');
Route::view('/help-center', 'pages.help-center')->name('pages.help-center');
Route::view('/how-to-buy', 'pages.how-to-buy')->name('pages.how-to-buy');
Route::view('/how-to-sell', 'pages.how-to-sell')->name('pages.how-to-sell');
Route::view('/safety-tips', 'pages.safety-tips')->name('pages.safety-tips');
Route::view('/faq', 'pages.faq')->name('pages.faq');
Route::view('/contact-us', 'pages.contact')->name('pages.contact');
Route::get('/government-services', [\App\Http\Controllers\Frontend\GovernmentServiceController::class, 'index'])->name('gov-services.index');
Route::get('/government-services/{slug}', [\App\Http\Controllers\Frontend\GovernmentServiceController::class, 'show'])->name('gov-services.show');
Route::post('/contact-us', [\App\Http\Controllers\Frontend\StaticPageController::class, 'contactSubmit'])->name('pages.contact.submit');

Route::get('/explore', [\App\Http\Controllers\Frontend\ExploreController::class, 'index'])->name('pages.explore.index');
Route::get('/explore/{slug}', [\App\Http\Controllers\Frontend\ExploreController::class, 'show'])->name('pages.explore.show');

Route::get('/towns', [\App\Http\Controllers\Frontend\TownController::class, 'index'])->name('towns.index');
Route::get('/town', fn () => redirect('/towns'));
Route::get('/town/{slug}', [\App\Http\Controllers\Frontend\TownController::class, 'show'])->name('town.show');

Route::get('/events', [\App\Http\Controllers\Frontend\EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar/{year}/{month}', [\App\Http\Controllers\Frontend\EventController::class, 'monthly'])->name('events.monthly');
Route::get('/events/{slug}', [\App\Http\Controllers\Frontend\EventController::class, 'show'])->name('events.show');

Route::view('/saved', 'frontend.saved')->name('saved');

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
});

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
    Route::post('/reset-password', [AuthController::class, 'doResetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify-notice', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/resend', fn () => redirect()->route('verification.notice'));
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::view('/account-pending', 'auth.account-pending')->name('account.pending');
    Route::view('/account-suspended', 'auth.account-suspended')->name('account.suspended');
});

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

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

    Route::resource('listings', DashListing::class)->names('dashboard.listings');

    Route::view('membership', 'dashboard.memberships.index')->name('dashboard.membership');
    Route::view('payments', 'dashboard.payments.index')->name('dashboard.payments');
    Route::get('chat', [\App\Http\Controllers\Dashboard\ChatController::class, 'index'])->name('dashboard.chat');
    Route::get('chat/{thread}', [\App\Http\Controllers\Dashboard\ChatController::class, 'show'])->name('dashboard.chat.show');
    Route::post('chat', [\App\Http\Controllers\Dashboard\ChatController::class, 'store'])->name('dashboard.chat.store');
    Route::post('chat/{thread}/reply', [\App\Http\Controllers\Dashboard\ChatController::class, 'reply'])->name('dashboard.chat.reply');
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
    Route::post('deals', [\App\Http\Controllers\Dashboard\DealController::class, 'store'])->name('dashboard.deals.store');
    Route::get('deals/{deal}/edit', [\App\Http\Controllers\Dashboard\DealController::class, 'edit'])->name('dashboard.deals.edit');
    Route::put('deals/{deal}', [\App\Http\Controllers\Dashboard\DealController::class, 'update'])->name('dashboard.deals.update');
    Route::delete('deals/{deal}', [\App\Http\Controllers\Dashboard\DealController::class, 'destroy'])->name('dashboard.deals.destroy');

    Route::resource('stores', DashStore::class)->names('dashboard.stores');
    Route::get('stores/{store}/analytics', [DashStore::class, 'analytics'])->name('dashboard.stores.analytics');
    Route::get('stores/{store}/reviews', [DashStore::class, 'reviews'])->name('dashboard.stores.reviews');
    Route::resource('stores.products', StoreProductController::class)->names('dashboard.stores.products');
});

/*
|--------------------------------------------------------------------------
| Super Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/make-admin', [UserManagementController::class, 'makeAdmin'])->name('admin.users.makeAdmin');
    Route::post('/users/{user}/make-super-admin', [UserManagementController::class, 'makeSuperAdmin'])->name('admin.users.makeSuperAdmin');
    Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');
    Route::post('/users/{user}/toggle-multiple-stores', [UserManagementController::class, 'toggleMultipleStores'])->name('admin.users.toggleMultipleStores');
    Route::post('/users/{user}/store-limit', [UserManagementController::class, 'setStoreLimit'])->name('admin.users.storeLimit');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
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
    Route::get('/classifieds', [AdminListingController::class, 'classifieds'])->name('admin.classifieds.index');
    Route::get('/listings/create', [AdminListingController::class, 'create'])->name('admin.listings.create');
    Route::post('/listings', [AdminListingController::class, 'store'])->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('admin.listings.edit');
    Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('admin.listings.update');
    Route::post('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('admin.listings.approve');
    Route::post('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('admin.listings.reject');
    Route::post('/listings/{listing}/feature', [AdminListingController::class, 'feature'])->name('admin.listings.feature');
    Route::post('/listings/{listing}/top', [AdminListingController::class, 'top'])->name('admin.listings.top');

    Route::get('/categories', [CategoryManagementController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryManagementController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryManagementController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryManagementController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{category}/toggle', [CategoryManagementController::class, 'toggle'])->name('admin.categories.toggle');
    Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('admin.categories.destroy');

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
    Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('admin.payments.status');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    Route::get('/deals', [\App\Http\Controllers\Admin\DealManagementController::class, 'index'])->name('admin.deals.index');
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

    Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markRead'])->name('admin.notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.readAll');
});

Route::middleware('throttle:60,1')->group(function () {

Route::get('/api/search-suggestions', function (\Illuminate\Http\Request $request) {
    $q = trim($request->input('q', ''));
    if (strlen($q) < 2) return response()->json([]);
    $q = str_replace(['%', '_'], ['\%', '\_'], $q);
    $listings = \App\Models\Listing::published()
        ->where('title', 'like', '%'.$q.'%')
        ->with('category')
        ->latest()
        ->take(6)
        ->get(['id', 'title', 'slug', 'category_id']);
    return response()->json($listings->map(fn ($l) => [
        'title' => \Illuminate\Support\Str::limit($l->title, 50),
        'url' => '/listings/'.$l->slug,
        'category' => optional($l->category)->name,
    ]));
});

// ── Category fields API ──────────────────────────────────────
Route::get('/api/category-fields/{categoryId}', function ($categoryId) {
    $category = \App\Models\Category::with(['customFields' => fn ($q) => $q->orderByPivot('sort_order')])->find($categoryId);
    if (!$category) return response()->json(['fields' => []]);
    $parentSlug = optional($category->parent)->slug ?? $category->slug;
    $brandGroupMap = [
        'smartphones' => 'mobile', 'feature-phones' => 'mobile', 'tablets' => 'mobile',
        'smart-watches' => 'mobile',
        'computers' => 'computer', 'laptops' => 'computer',
        'tv-audio' => 'tv', 'cameras' => 'camera',
        'networking' => 'electronics', 'gaming' => 'electronics', 'smart-home' => 'electronics',
        'office-electronics' => 'electronics', 'electronic-components' => 'electronics',
        'cars' => 'vehicle', 'suvs-jeeps' => 'vehicle', 'vans' => 'vehicle', 'pickups' => 'vehicle',
        'three-wheelers' => 'vehicle', 'motorcycles' => 'vehicle_bike', 'buses' => 'vehicle',
        'trucks-lorries' => 'vehicle', 'tractors' => 'vehicle', 'heavy-machinery' => 'vehicle',
        'electric-vehicles' => 'vehicle', 'boats-watercraft' => 'vehicle_boat',
        'bicycles' => 'vehicle_bicycle',
        'kitchen-appliances' => 'home', 'large-appliances' => 'home',
        'cleaning-appliances' => 'home', 'cooling-heating' => 'home', 'small-appliances' => 'home',
        'furniture' => 'furniture', 'kitchen-dining' => 'home', 'bathroom' => 'home',
        'lighting' => 'home', 'decor' => 'home', 'garden' => 'home', 'home-improvement' => 'home',
        'pet-food' => 'pet',
        // Old slug compat
        'mobile-phones' => 'mobile', 'mobile-accessories' => 'mobile', 'mobile-spare-parts' => 'mobile',
        'smart-products' => 'mobile', 'computers-laptops-tablets' => 'computer',
        'computer-accessories' => 'computer', 'tv' => 'tv', 'tv-accessories' => 'tv',
        'camera' => 'camera', 'audio-mp3' => 'electronics', 'electronic-home-appliances' => 'electronics',
        'video-games-other-electronics' => 'electronics', 'aircon-fittings' => 'electronics',
        'bikes' => 'vehicle_bike', 'lorries' => 'vehicle', 'heavy-duty' => 'vehicle',
        'tractor' => 'vehicle', 'boats' => 'vehicle_boat', 'bicycle' => 'vehicle_bicycle',
        'bathrooms' => 'home', 'kitchen-items' => 'home', 'other-items' => 'home',
        'animal-accessories' => 'pet',
    ];
    $brandGroup = $brandGroupMap[$category->slug] ?? null;
    $autoTitle = in_array($brandGroup, ['mobile', 'computer', 'tv', 'camera', 'vehicle', 'vehicle_bike', 'vehicle_boat']);
    return response()->json([
        'fields' => $category->customFields->map(fn ($f) => [
            'id' => $f->id, 'label' => $f->label, 'name' => $f->name, 'type' => $f->type,
            'options' => $f->options, 'placeholder' => $f->placeholder,
            'is_required' => (bool) $f->pivot->is_required,
        ]),
        'brand_group' => $brandGroup,
        'auto_title' => $autoTitle,
        'category_name' => $category->name,
        'parent_slug' => $parentSlug,
    ]);
});

Route::get('/api/brands/{group?}', function ($group = null) {
    return response()->json(\App\Models\Brand::active()->get(['id', 'name']));
});

Route::get('/api/brand-models/{brandId}', function ($brandId) {
    return response()->json(\App\Models\BrandModel::where('brand_id', $brandId)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']));
});

Route::get('/api/subcategories/{parentId}', function ($parentId) {
    return response()->json(\App\Models\Category::where('parent_id', $parentId)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'icon']));
});

}); // end throttle:60,1 group

Route::fallback(fn () => abort(404));
