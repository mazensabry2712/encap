<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'categoryCount' => Category::query()->count(),
            'publishedCategoryCount' => Category::query()->where('is_published', true)->count(),
            'draftCategoryCount' => Category::query()->where('is_published', false)->count(),
            'companyCount' => Company::query()->count(),
            'publishedCompanyCount' => Company::query()->where('is_published', true)->count(),
            'quoteRequestCount' => QuoteRequest::query()->count(),
            'newQuoteRequestCount' => QuoteRequest::query()->where('status', 'new')->count(),
            'contactMessageCount' => ContactMessage::query()->count(),
            'newContactMessageCount' => ContactMessage::query()->where('status', 'new')->count(),
            'recentQuoteRequests' => QuoteRequest::query()->with('category')->latest()->limit(5)->get(),
            'recentContactMessages' => ContactMessage::query()->latest()->limit(5)->get(),
        ]);
    }
}
