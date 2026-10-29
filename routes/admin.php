<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageLibraryController;
use App\Http\Controllers\Admin\QuoteRequestController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Middleware\SetAdminLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(SetAdminLocale::class)->group(function () {
    Route::post('/language', function (Request $request) {
        $validated = $request->validate(['locale' => ['required', 'in:en,ar']]);
        $request->session()->put('admin_language', $validated['locale']);

        return back();
    })->name('language');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/images/prepare', [ImageLibraryController::class, 'prepare'])->middleware('throttle:20,1')->name('images.prepare');
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media/upload', [MediaController::class, 'upload'])->middleware('throttle:20,1')->name('media.upload');
        Route::post('/media/replace', [MediaController::class, 'replace'])->middleware('throttle:20,1')->name('media.replace');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('companies', CompanyController::class)->only(['index', 'edit', 'update']);
        Route::get('/seo', [SeoSettingController::class, 'edit'])->name('seo.edit');
        Route::put('/seo', [SeoSettingController::class, 'update'])->name('seo.update');
        Route::get('/quote-requests', [QuoteRequestController::class, 'index'])->name('quote-requests.index');
        Route::patch('/quote-requests/{quoteRequest}', [QuoteRequestController::class, 'update'])->name('quote-requests.update');
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::patch('/contact-messages/{contactMessage}', [ContactMessageController::class, 'update'])->name('contact-messages.update');
    });
});
