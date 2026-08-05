<?php

use App\Http\Controllers\Frontend\OnlineOrderController;
use App\Http\Controllers\Frontend\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Frontend Routes — MarketPro E-Commerce Shop (Inertia/React)
|--------------------------------------------------------------------------
|
| ARCHITECTURE: Hybrid
|   - These routes serve the React/Inertia frontend (MarketPro shop)
|   - The backend (Company Admin, Super Admin) remains 100% Blade
|
| TENANT RESOLUTION:
|   - IdentifyTenantByDomain middleware runs globally (registered in bootstrap/app.php)
|   - It resolves the tenant from the subdomain/custom_domain and binds to app('tenant')
|   - No need to add it here — it runs automatically on every web request
|
| INERTIA DATA FLOW:
|   Each route renders its OWN dedicated Inertia page component (not the generic 'Welcome')
|   This is the critical fix from the INERTIA_READINESS_REPORT — each page gets its own data
|
*/

Route::middleware('web')->group(function () {

    // ── Data-driven routes (served by ShopController with real DB data) ────────

    Route::get('/', [ShopController::class, 'home'])
        ->name('frontend.home');

    Route::get('/shop', [ShopController::class, 'shop'])
        ->name('frontend.shop');

    Route::get('/product/{id}', [ShopController::class, 'productDetail'])
        ->where('id', '[0-9]+')
        ->name('frontend.product');

    Route::get('/checkout', [ShopController::class, 'checkout'])
        ->name('frontend.checkout');

    Route::get('/cart', [ShopController::class, 'cart'])
        ->name('frontend.cart');

    // ── Online Order API (POST — returns JSON, consumed by React) ─────────────
    // throttle:30,1 = max 30 order attempts per IP per minute.
    // This matches the spirit of the POS checkout throttle (throttle:60,1) while
    // being tighter for the public-facing endpoint (no auth required).

    Route::post('/order', [OnlineOrderController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('frontend.order.store');

    Route::get('/order/confirmation/{invoiceNo}', [OnlineOrderController::class, 'confirmation'])
        ->name('frontend.order.confirmation');

    // ── Static/UI-only routes (no DB data needed) ─────────────────────────────

    Route::get('/index-two', fn() => Inertia::render('MarketPro/HomePageTwo'))
        ->name('frontend.home-two');

    Route::get('/index-three', fn() => Inertia::render('MarketPro/HomePageThree'))
        ->name('frontend.home-three');

    Route::get('/product-details', fn() => Inertia::render('MarketPro/ProductDetailsPageOne'))
        ->name('frontend.product-details');

    Route::get('/product-details-two', fn() => Inertia::render('MarketPro/ProductDetailsPageTwo'))
        ->name('frontend.product-details-two');

    Route::get('/wishlist', fn() => Inertia::render('MarketPro/WishlistPage'))
        ->name('frontend.wishlist');

    Route::get('/account', fn() => Inertia::render('MarketPro/AccountPage'))
        ->name('frontend.account');

    Route::get('/blog', fn() => Inertia::render('MarketPro/BlogPage'))
        ->name('frontend.blog');

    Route::get('/blog-details', fn() => Inertia::render('MarketPro/BlogDetailsPage'))
        ->name('frontend.blog-details');

    Route::get('/contact', fn() => Inertia::render('MarketPro/ContactPage'))
        ->name('frontend.contact');

    Route::get('/vendor', fn() => Inertia::render('MarketPro/VendorPage'))
        ->name('frontend.vendor');

    Route::get('/vendor-details', fn() => Inertia::render('MarketPro/VendorDetailsPage'))
        ->name('frontend.vendor-details');

    Route::get('/vendor-two', fn() => Inertia::render('MarketPro/VendorTwoPage'))
        ->name('frontend.vendor-two');

    Route::get('/vendor-two-details', fn() => Inertia::render('MarketPro/VendorTwoDetailsPage'))
        ->name('frontend.vendor-two-details');

    Route::get('/become-seller', fn() => Inertia::render('MarketPro/BecomeSellerPage'))
        ->name('frontend.become-seller');
});
