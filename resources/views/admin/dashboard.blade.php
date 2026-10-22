@extends('admin.layout')
@section('page-title', 'Dashboard')

@section('content')
<div class="admin-dashboard">
    <section class="admin-dashboard-welcome">
        <div>
            <span class="admin-eyebrow">WORKSPACE OVERVIEW</span>
            <h2>Good work starts here.</h2>
            <p>Manage the public catalogue, review export enquiries, and keep your website information current.</p>
        </div>
        <div class="admin-dashboard-primary-actions">
            <a class="admin-btn" href="{{ route('admin.categories.create') }}"><span aria-hidden="true">＋</span> Add product category</a>
            <a class="admin-btn secondary" href="{{ route('home', ['locale' => 'en']) }}" target="_blank" rel="noopener">View website <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    <section class="admin-stat-grid" aria-label="Website overview">
        <a class="admin-stat-card" href="{{ route('admin.categories.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">▦</span>
            <span class="admin-stat-label">Product categories</span>
            <strong class="admin-stat-value">{{ number_format($categoryCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($publishedCategoryCount) }}</b> published <span aria-hidden="true">·</span> {{ number_format($draftCategoryCount) }} drafts</span>
            <span class="admin-stat-link">Manage catalogue <span aria-hidden="true">→</span></span>
        </a>
        <a class="admin-stat-card" href="{{ route('admin.quote-requests.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">↗</span>
            <span class="admin-stat-label">Export quote requests</span>
            <strong class="admin-stat-value">{{ number_format($quoteRequestCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($newQuoteRequestCount) }}</b> new {{ $newQuoteRequestCount === 1 ? 'request' : 'requests' }} to review</span>
            <span class="admin-stat-link">Open enquiries <span aria-hidden="true">→</span></span>
        </a>
        <a class="admin-stat-card" href="{{ route('admin.contact-messages.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">✉</span>
            <span class="admin-stat-label">Contact messages</span>
            <strong class="admin-stat-value">{{ number_format($contactMessageCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($newContactMessageCount) }}</b> new {{ $newContactMessageCount === 1 ? 'message' : 'messages' }}</span>
            <span class="admin-stat-link">View inbox <span aria-hidden="true">→</span></span>
        </a>
    </section>

    <div class="admin-dashboard-lower">
        <section class="admin-panel admin-dashboard-panel">
            <div class="admin-panel-head">
                <div><h2>Latest quote requests</h2><p>Recent enquiries from potential buyers and importers.</p></div>
                <a class="admin-panel-link" href="{{ route('admin.quote-requests.index') }}">View all <span aria-hidden="true">→</span></a>
            </div>
            @if($recentQuoteRequests->isNotEmpty())
                <div class="admin-dashboard-records">
                    @foreach($recentQuoteRequests as $item)
                        <article class="admin-dashboard-record">
                            <div class="admin-record-mark" aria-hidden="true">↗</div>
                            <div class="admin-record-copy">
                                <strong>{{ $item->contact_name }}</strong>
                                <span>{{ $item->company_name ?: $item->email }}</span>
                                <small>{{ $item->category?->name_en ?? 'Product not specified' }}@if($item->country) <span aria-hidden="true">·</span> {{ $item->country }}@endif</small>
                            </div>
                            <div class="admin-record-meta">
                                <span class="admin-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                <time datetime="{{ $item->created_at->toIso8601String() }}">{{ $item->created_at->format('M j, Y') }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-dashboard-empty">
                    <span aria-hidden="true">↗</span>
                    <strong>No quote requests yet</strong>
                    <p>When a visitor submits an export enquiry, it will appear here.</p>
                </div>
            @endif
        </section>

        <section class="admin-panel admin-dashboard-panel">
            <div class="admin-panel-head">
                <div><h2>Latest contact messages</h2><p>Messages received through the public website.</p></div>
                <a class="admin-panel-link" href="{{ route('admin.contact-messages.index') }}">View all <span aria-hidden="true">→</span></a>
            </div>
            @if($recentContactMessages->isNotEmpty())
                <div class="admin-dashboard-records">
                    @foreach($recentContactMessages as $item)
                        <article class="admin-dashboard-record">
                            <div class="admin-record-mark message" aria-hidden="true">✉</div>
                            <div class="admin-record-copy">
                                <strong>{{ $item->subject }}</strong>
                                <span>{{ $item->name }} <span aria-hidden="true">·</span> {{ $item->email }}</span>
                                <small>{{ $item->company_name ?: 'General enquiry' }}</small>
                            </div>
                            <div class="admin-record-meta">
                                <span class="admin-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                <time datetime="{{ $item->created_at->toIso8601String() }}">{{ $item->created_at->format('M j, Y') }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-dashboard-empty">
                    <span aria-hidden="true">✉</span>
                    <strong>Your inbox is clear</strong>
                    <p>New contact form submissions will show up here.</p>
                </div>
            @endif
        </section>
    </div>

    <section class="admin-quick-access">
        <div><span class="admin-eyebrow">QUICK ACCESS</span><h2>What would you like to update?</h2></div>
        <div class="admin-quick-links">
            <a href="{{ route('admin.categories.index') }}"><span aria-hidden="true">▦</span><span><strong>Product catalogue</strong><small>Edit images, descriptions and page sections</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.seo.edit') }}"><span aria-hidden="true">⌕</span><span><strong>SEO & site settings</strong><small>Manage site metadata and contact details</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.quote-requests.index') }}"><span aria-hidden="true">⇢</span><span><strong>Sales enquiries</strong><small>Track request status and buyer requirements</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
        </div>
    </section>
</div>
@endsection
