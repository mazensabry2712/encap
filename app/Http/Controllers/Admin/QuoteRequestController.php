<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.quote-requests.index', [
            'requests' => QuoteRequest::query()->with('category')->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,reviewing,quoted,accepted,rejected'],
        ]);

        $quoteRequest->update($data);

        return back()->with('success', __('Quote request status updated.'));
    }
}
