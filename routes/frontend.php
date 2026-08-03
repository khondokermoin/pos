<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web', 'inertia'])->group(function () {
    $frontendRoutes = [
        '/',
        '/index-two',
        '/index-three',
        '/shop',
        '/product-details',
        '/product-details-two',
        '/cart',
        '/checkout',
        '/become-seller',
        '/wishlist',
        '/account',
        '/blog',
        '/blog-details',
        '/contact',
        '/vendor',
        '/vendor-details',
        '/vendor-two',
        '/vendor-two-details',
    ];

    foreach ($frontendRoutes as $path) {
        Route::get($path, function () {
            return Inertia::render('Welcome');
        });
    }

    Route::fallback(function () {
        return Inertia::render('Welcome');
    });
});
