<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\LocationManagementController;
use App\Http\Controllers\Admin\MembershipController;
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

Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('stores.show');

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/classified', [ListingController::class, 'index'])->defaults('type', 'classified')->name('classified.index');
Route::get('/listing/{slug}', [ListingController::class, 'show'])->name('listing.show');
Route::get('/listings/{slug}', [ListingController::class, 'show'])->name('listings.show');

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
Route::post('/contact-us', [\App\Http\Controllers\Frontend\StaticPageController::class, 'contactSubmit'])->name('pages.contact.submit');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'doRegister'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify-notice', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');

/*
|--------------------------------------------------------------------------
| User + Store Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified.custom'])->prefix('dashboard')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'edit'])->name('dashboard.profile');
    Route::put('/profile', [\App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('dashboard.profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Dashboard\ProfileController::class, 'updatePassword'])->name('dashboard.profile.password');

    Route::resource('listings', DashListing::class)->names('dashboard.listings');

    Route::view('membership', 'dashboard.memberships.index')->name('dashboard.membership');
    Route::view('payments', 'dashboard.payments.index')->name('dashboard.payments');
    Route::view('chat', 'dashboard.chats.index')->name('dashboard.chat');
    Route::view('reviews', 'dashboard.reviews.index')->name('dashboard.reviews');
    Route::get('favorites', [\App\Http\Controllers\Dashboard\FavoriteController::class, 'index'])->name('dashboard.favorites');
    Route::post('favorites/{listing}/toggle', [\App\Http\Controllers\Dashboard\FavoriteController::class, 'toggle'])->name('dashboard.favorites.toggle');

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
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/make-admin', [UserManagementController::class, 'makeAdmin'])->name('admin.users.makeAdmin');
    Route::post('/users/{user}/make-super-admin', [UserManagementController::class, 'makeSuperAdmin'])->name('admin.users.makeSuperAdmin');
    Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');

    Route::get('/stores', [ModerationController::class, 'stores'])->name('admin.stores.index');
    Route::post('/stores/{store}/approve', [ModerationController::class, 'approveStore'])->name('admin.stores.approve');
    Route::post('/stores/{store}/suspend', [ModerationController::class, 'suspendStore'])->name('admin.stores.suspend');
    Route::post('/stores/{store}/feature', [ModerationController::class, 'featureStore'])->name('admin.stores.feature');

    Route::get('/listings', [ModerationController::class, 'listings'])->name('admin.listings.index');
    Route::get('/listings/create', [ModerationController::class, 'createListing'])->name('admin.listings.create');
    Route::post('/listings', [ModerationController::class, 'storeListing'])->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [ModerationController::class, 'editListing'])->name('admin.listings.edit');
    Route::put('/listings/{listing}', [ModerationController::class, 'updateListing'])->name('admin.listings.update');
    Route::post('/listings/{listing}/approve', [ModerationController::class, 'approveListing'])->name('admin.listings.approve');
    Route::post('/listings/{listing}/reject', [ModerationController::class, 'rejectListing'])->name('admin.listings.reject');
    Route::post('/listings/{listing}/feature', [ModerationController::class, 'featureListing'])->name('admin.listings.feature');
    Route::post('/listings/{listing}/top', [ModerationController::class, 'topListing'])->name('admin.listings.top');

    Route::get('/categories', [CategoryManagementController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryManagementController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryManagementController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryManagementController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{category}/toggle', [CategoryManagementController::class, 'toggle'])->name('admin.categories.toggle');
    Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('admin.categories.destroy');

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
});

Route::fallback(fn () => abort(404));
