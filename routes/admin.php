<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuoteRequestController;
use App\Http\Controllers\Admin\SeoSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.categories.index'))->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::get('/seo', [SeoSettingController::class, 'edit'])->name('seo.edit');
        Route::put('/seo', [SeoSettingController::class, 'update'])->name('seo.update');
        Route::get('/quote-requests', [QuoteRequestController::class, 'index'])->name('quote-requests.index');
        Route::patch('/quote-requests/{quoteRequest}', [QuoteRequestController::class, 'update'])->name('quote-requests.update');
    });
});
