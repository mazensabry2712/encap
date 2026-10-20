<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function home(): View
    {
        $categories = Category::query()->where('is_published', true)
            ->orderBy('sort_order')->orderBy('name_en')->limit(10)->get();

        return view('website.home', compact('categories'));
    }

    public function products(): View
    {
        $categories = Category::query()->where('is_published', true)
            ->orderBy('sort_order')->orderBy('name_en')->paginate(12);

        return view('website.products.index', compact('categories'));
    }

    public function category(string $locale, Category $category): View
    {
        abort_unless($category->is_published, 404);

        return view('website.products.show', compact('category'));
    }

    public function quote(Request $request): View
    {
        $categories = Category::query()->where('is_published', true)
            ->orderBy('name_en')->get(['id', 'name_en', 'name_ar']);

        return view('website.quote', [
            'categories' => $categories,
            'selectedCategory' => $request->integer('category') ?: null,
        ]);
    }

    public function storeQuote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'contact_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'country' => ['required', 'string', 'max:100'],
            'quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999999'],
            'quantity_unit' => ['nullable', 'string', 'max:40'],
            'product_form' => ['nullable', 'in:fresh,frozen,processed'],
            'packaging_requirements' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (!empty($validated['category_id'])) {
            abort_unless(
                Category::query()->whereKey($validated['category_id'])->where('is_published', true)->exists(),
                422
            );
        }

        QuoteRequest::create($validated);

        return back()->with('success', __('Your request has been received. Our team will contact you soon.'));
    }
}
