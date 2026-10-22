@extends('admin.layout')
@section('page-title', __('Dashboard'))

@section('content')
<div class="admin-dashboard">
    <section class="admin-dashboard-welcome">
        <div>
            <span class="admin-eyebrow">{{ __('WORKSPACE OVERVIEW') }}</span>
            <h2>{{ __('Good work starts here.') }}</h2>
            <p>{{ __('Manage the public catalogue, review export enquiries, and keep your website information current.') }}</p>
        </div>
        <div class="admin-dashboard-primary-actions">
            <a class="admin-btn" href="{{ route('admin.categories.create') }}"><span aria-hidden="true">＋</span> {{ __('Add product category') }}</a>
            <a class="admin-btn secondary" href="{{ route('home', ['locale' => 'en']) }}" target="_blank" rel="noopener">{{ __('View website') }} <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    <section class="admin-stat-grid" aria-label="{{ __('Website overview') }}">
        <a class="admin-stat-card" href="{{ route('admin.categories.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">▦</span>
            <span class="admin-stat-label">{{ __('Product categories') }}</span>
            <strong class="admin-stat-value">{{ number_format($categoryCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($publishedCategoryCount) }}</b> {{ __('published') }} <span aria-hidden="true">·</span> {{ number_format($draftCategoryCount) }} {{ __('drafts') }}</span>
            <span class="admin-stat-link">{{ __('Manage catalogue') }} <span aria-hidden="true">→</span></span>
        </a>
        <a class="admin-stat-card" href="{{ route('admin.quote-requests.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">↗</span>
            <span class="admin-stat-label">{{ __('Export quote requests') }}</span>
            <strong class="admin-stat-value">{{ number_format($quoteRequestCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($newQuoteRequestCount) }}</b> {{ __('new quote requests need review') }}</span>
            <span class="admin-stat-link">{{ __('Open enquiries') }} <span aria-hidden="true">→</span></span>
        </a>
        <a class="admin-stat-card" href="{{ route('admin.contact-messages.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">✉</span>
            <span class="admin-stat-label">{{ __('Contact messages') }}</span>
            <strong class="admin-stat-value">{{ number_format($contactMessageCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($newContactMessageCount) }}</b> {{ __('new contact messages') }}</span>
            <span class="admin-stat-link">{{ __('View inbox') }} <span aria-hidden="true">→</span></span>
        </a>
    </section>

    <div class="admin-dashboard-lower">
        <section class="admin-panel admin-dashboard-panel">
            <div class="admin-panel-head">
                <div><h2>{{ __('Latest quote requests') }}</h2><p>{{ __('Recent enquiries from potential buyers and importers.') }}</p></div>
                <a class="admin-panel-link" href="{{ route('admin.quote-requests.index') }}">{{ __('View all') }} <span aria-hidden="true">→</span></a>
            </div>
            @if($recentQuoteRequests->isNotEmpty())
                <div class="admin-dashboard-records">
                    @foreach($recentQuoteRequests as $item)
                        <article class="admin-dashboard-record">
                            <div class="admin-record-mark" aria-hidden="true">↗</div>
                            <div class="admin-record-copy">
                                <strong>{{ $item->contact_name }}</strong>
                                <span>{{ $item->company_name ?: $item->email }}</span>
                                <small>{{ app()->getLocale() === 'ar' && $item->category?->name_ar ? $item->category->name_ar : ($item->category?->name_en ?? __('Product not specified')) }}@if($item->country) <span aria-hidden="true">·</span> {{ $item->country }}@endif</small>
                            </div>
                            <div class="admin-record-meta">
                                <span class="admin-badge {{ $item->status }}">{{ __(ucfirst($item->status)) }}</span>
                                <time datetime="{{ $item->created_at->toIso8601String() }}">{{ $item->created_at->format('M j, Y') }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-dashboard-empty">
                    <span aria-hidden="true">↗</span>
                    <strong>{{ __('No quote requests yet') }}</strong>
                    <p>{{ __('When a visitor submits an export enquiry, it will appear here.') }}</p>
                </div>
            @endif
        </section>

        <section class="admin-panel admin-dashboard-panel">
            <div class="admin-panel-head">
                <div><h2>{{ __('Latest contact messages') }}</h2><p>{{ __('Messages received through the public website.') }}</p></div>
                <a class="admin-panel-link" href="{{ route('admin.contact-messages.index') }}">{{ __('View all') }} <span aria-hidden="true">→</span></a>
            </div>
            @if($recentContactMessages->isNotEmpty())
                <div class="admin-dashboard-records">
                    @foreach($recentContactMessages as $item)
                        <article class="admin-dashboard-record">
                            <div class="admin-record-mark message" aria-hidden="true">✉</div>
                            <div class="admin-record-copy">
                                <strong>{{ $item->subject }}</strong>
                                <span>{{ $item->name }} <span aria-hidden="true">·</span> {{ $item->email }}</span>
                                <small>{{ $item->company_name ?: __('General enquiry') }}</small>
                            </div>
                            <div class="admin-record-meta">
                                <span class="admin-badge {{ $item->status }}">{{ __(ucfirst($item->status)) }}</span>
                                <time datetime="{{ $item->created_at->toIso8601String() }}">{{ $item->created_at->format('M j, Y') }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-dashboard-empty">
                    <span aria-hidden="true">✉</span>
                    <strong>{{ __('Your inbox is clear') }}</strong>
                    <p>{{ __('New contact form submissions will show up here.') }}</p>
                </div>
            @endif
        </section>
    </div>

    <section class="admin-quick-access">
        <div><span class="admin-eyebrow">{{ __('QUICK ACCESS') }}</span><h2>{{ __('What would you like to update?') }}</h2></div>
        <div class="admin-quick-links">
            <a href="{{ route('admin.categories.index') }}"><span aria-hidden="true">▦</span><span><strong>{{ __('Product catalogue') }}</strong><small>{{ __('Edit images, descriptions and page sections') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.seo.edit') }}"><span aria-hidden="true">⌕</span><span><strong>{{ __('SEO & site settings') }}</strong><small>{{ __('Manage site metadata and contact details') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.quote-requests.index') }}"><span aria-hidden="true">⇢</span><span><strong>{{ __('Sales enquiries') }}</strong><small>{{ __('Track request status and buyer requirements') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
        </div>
    </section>
</div>
@endsection
