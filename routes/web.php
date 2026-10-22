<?php

use App\Http\Controllers\WebsiteController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/en', 302);
Route::pattern('locale', 'en|ar');

Route::get('/robots.txt', fn () => response()->view('seo.robots')->header('Content-Type', 'text/plain; charset=UTF-8'))
    ->name('seo.robots');

Route::get('/sitemap.xml', function () {
    return response()->view('seo.sitemap', [
        'locales' => ['en', 'ar'],
        'categories' => Category::query()->where('is_published', true)->orderBy('sort_order')->get(['slug', 'updated_at', 'name_ar', 'description_ar']),
    ])->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('seo.sitemap');

Route::prefix('{locale}')->group(function () {
    Route::get('/', [WebsiteController::class, 'home'])->name('home');
    Route::get('/about', [WebsiteController::class, 'contentPage'])->defaults('page', 'about')->name('about');
    Route::get('/farms', [WebsiteController::class, 'contentPage'])->defaults('page', 'farms')->name('farms');
    Route::get('/packing-stations', [WebsiteController::class, 'contentPage'])->defaults('page', 'packing-stations')->name('packing-stations');
    Route::get('/quality', [WebsiteController::class, 'contentPage'])->defaults('page', 'quality')->name('quality');
    Route::get('/export', [WebsiteController::class, 'contentPage'])->defaults('page', 'export')->name('export');
    Route::get('/gallery', [WebsiteController::class, 'contentPage'])->defaults('page', 'gallery')->name('gallery');
    Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
    Route::post('/contact', [WebsiteController::class, 'storeContact'])
        ->middleware('throttle:5,1')->name('contact.store');
    Route::get('/products', [WebsiteController::class, 'products'])->name('products.index');
    Route::get('/products/{category:slug}', [WebsiteController::class, 'category'])->name('products.show');
    Route::get('/request-a-quote', [WebsiteController::class, 'quote'])->name('quote.create');
    Route::post('/request-a-quote', [WebsiteController::class, 'storeQuote'])
        ->middleware('throttle:5,1')->name('quote.store');
});

require __DIR__.'/admin.php';
