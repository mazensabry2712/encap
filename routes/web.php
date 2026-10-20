<?php

use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/en', 302);
Route::pattern('locale', 'en|ar');

Route::prefix('{locale}')->group(function () {
    Route::get('/', [WebsiteController::class, 'home'])->name('home');
    Route::get('/products', [WebsiteController::class, 'products'])->name('products.index');
    Route::get('/products/{category:slug}', [WebsiteController::class, 'category'])->name('products.show');
    Route::get('/request-a-quote', [WebsiteController::class, 'quote'])->name('quote.create');
    Route::post('/request-a-quote', [WebsiteController::class, 'storeQuote'])
        ->middleware('throttle:5,1')->name('quote.store');
});

Route::view('/robots.txt', 'seo.robots')->name('seo.robots');
