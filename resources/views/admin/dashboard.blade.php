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
            <a class="admin-btn secondary" href="{{ route('home', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener">{{ __('View website') }} <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    <section class="admin-stat-grid" aria-label="{{ __('Website overview') }}">
        <a class="admin-stat-card" href="{{ route('admin.categories.index') }}">
            <span class="admin-stat-icon" aria-hidden="true">▦</span>
            <span class="admin-stat-label">{{ __('Product categories') }}</span>
            <strong class="admin-stat-value">{{ number_format($categoryCount) }}</strong>
            <span class="admin-stat-note"><b>{{ number_format($publishedCategoryCount) }}</b> {{ __('published') }} <span aria-hidden="true">·</span> {{ number_format($draftCategoryCount) }} {{ __('drafts') }}</span>
            <span class="admin-stat-note"><b>{{ number_format($frozenCategoryCount) }}</b> {{ __('Frozen products assigned') }}</span>
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

    <section class="admin-quick-access" aria-label="{{ __('Public website pages') }}">
        <div><span class="admin-eyebrow">{{ __('PUBLIC WEBSITE') }}</span><h2>{{ __('Preview public pages') }}</h2></div>
        <div class="admin-quick-links">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">⌂</span><span><strong>{{ __('Home') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('about', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">ⓘ</span><span><strong>{{ __('About') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('products.index', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">▦</span><span><strong>{{ __('Products') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('frozen-products', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">❄</span><span><strong>{{ __('Frozen') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('companies', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">⌘</span><span><strong>{{ __('Our Companies') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('farms', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">♧</span><span><strong>{{ __('Agriculture & Farms') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('packing-stations', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">▤</span><span><strong>{{ __('Packing Stations') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('export', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">↗</span><span><strong>{{ __('Export & Logistics') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('quality', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">✓</span><span><strong>{{ __('Quality') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">✉</span><span><strong>{{ __('Contact') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('quote.create', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener"><span aria-hidden="true">↗</span><span><strong>{{ __('Request a Quote') }}</strong><small>{{ __('Open this page as a visitor') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.seo.edit') }}"><span aria-hidden="true">⌕</span><span><strong>{{ __('Website content & SEO') }}</strong><small>{{ __('Edit public page text, page metadata and contact details.') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
        </div>
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

    @if(session('imageLibraryResult'))
        @php($imageLibraryResult = session('imageLibraryResult'))
        <section class="admin-panel" role="status" aria-labelledby="image-library-result-title">
            <div class="admin-panel-head">
                <div>
                    <h2 id="image-library-result-title">{{ __('Image library setup results') }}</h2>
                    <p>{{ __('Downloaded: :downloaded · Already present: :kept · Existing uploads copied: :copied', ['downloaded' => $imageLibraryResult['downloaded'], 'kept' => $imageLibraryResult['kept'], 'copied' => $imageLibraryResult['copied']]) }}</p>
                </div>
                <span class="admin-badge {{ $imageLibraryResult['complete'] ? '' : 'draft' }}">{{ $imageLibraryResult['complete'] ? __('Complete') : (count($imageLibraryResult['errors']) ? __('Needs retry') : __('In progress')) }}</span>
            </div>
            @if(count($imageLibraryResult['errors']))
                <ul>
                    @foreach($imageLibraryResult['errors'] as $imageLibraryError)
                        <li>{{ $imageLibraryError }}</li>
                    @endforeach
                </ul>
            @endif
            @if(!$imageLibraryResult['finished'])
                <p>{{ __('Image preparation is processed in short batches to avoid shared-hosting request timeouts.') }}</p>
                <form method="post" action="{{ route('admin.images.prepare') }}">
                    @csrf
                    <input type="hidden" name="offset" value="{{ $imageLibraryResult['next_offset'] }}">
                    <button class="admin-btn" type="submit">{{ __('Continue image preparation') }} ({{ min($imageLibraryResult['next_offset'] + 1, $imageLibraryResult['total']) }}–{{ min($imageLibraryResult['next_offset'] + 4, $imageLibraryResult['total']) }} / {{ $imageLibraryResult['total'] }})</button>
                </form>
            @elseif(count($imageLibraryResult['errors']))
                <p>{{ __('Some images could not be prepared. Retry the library setup after checking the hosting connection and folder permissions.') }}</p>
                <form method="post" action="{{ route('admin.images.prepare') }}">
                    @csrf
                    <input type="hidden" name="offset" value="0">
                    <button class="admin-btn secondary" type="submit">{{ __('Retry missing images') }}</button>
                </form>
            @else
                <p>{{ __('The root-level image folder is ready. Existing uploads in the old location were kept as a backup.') }}</p>
            @endif
        </section>
    @endif

    <section class="admin-panel" aria-labelledby="image-library-title">
        <div class="admin-panel-head">
            <div>
                <h2 id="image-library-title">{{ __('Image library') }}</h2>
                <p>{{ __('Save site photography and move current product/SEO uploads into encap-img, outside public. Old copies are not deleted.') }}</p>
            </div>
            <div class="admin-actions">
                <a class="admin-btn secondary" href="{{ route('admin.media.index') }}">{{ __('Manage all site images') }}</a>
                <form method="post" action="{{ route('admin.images.prepare') }}">
                    @csrf
                    <button class="admin-btn" type="submit">{{ __('Prepare image library') }} ↗</button>
                </form>
            </div>
        </div>
    </section>

    <section class="admin-quick-access">
        <div><span class="admin-eyebrow">{{ __('QUICK ACCESS') }}</span><h2>{{ __('What would you like to update?') }}</h2></div>
        <div class="admin-quick-links">
            <a href="{{ route('admin.categories.index') }}"><span aria-hidden="true">▦</span><span><strong>{{ __('Product catalogue') }}</strong><small>{{ __('Edit images, descriptions and page sections') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.media.index') }}"><span aria-hidden="true">▧</span><span><strong>{{ __('Central image library') }}</strong><small>{{ __('Replace site photos, logos, icons and product images') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.seo.edit') }}"><span aria-hidden="true">⌕</span><span><strong>{{ __('SEO & site settings') }}</strong><small>{{ __('Manage site metadata and contact details') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
            <a href="{{ route('admin.quote-requests.index') }}"><span aria-hidden="true">⇢</span><span><strong>{{ __('Sales enquiries') }}</strong><small>{{ __('Track request status and buyer requirements') }}</small></span><span class="admin-quick-arrow" aria-hidden="true">↗</span></a>
        </div>
    </section>
</div>
@endsection
