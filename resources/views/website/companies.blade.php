@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('content')
<section class="encap-catalog-hero" aria-labelledby="encap-companies-title">
    <div class="container">
        <nav class="encap-catalog-breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ __('Our Companies') }}</span>
        </nav>

        <div class="encap-catalog-hero-grid" data-encap-hero>
            <div class="encap-catalog-hero-copy">
                <div class="encap-catalog-eyebrow">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-network"></use></svg>
                    <span>{{ __('ENCAP GROUP · EGYPT & SAUDI ARABIA') }}</span>
                    <i aria-hidden="true"></i>
                    <span>{{ __('Group Companies') }}</span>
                </div>
                <h1 id="encap-companies-title">{{ $companiesContent['title'] ?? __('Our Companies') }}</h1>
                <p class="encap-catalog-hero-tagline">{{ $companiesContent['hero_tagline'] ?? __('A connected group, with a presence across markets.') }}</p>
                <p class="encap-catalog-hero-description">{{ $companiesContent['hero_description'] ?? __('Explore the companies affiliated with ENCAP, learn about their locations and contact our team for business enquiries.') }}</p>
                <div class="encap-catalog-hero-actions">
                    <a class="encap-catalog-button encap-catalog-button--light" href="#encap-companies-grid">
                        {{ __('Explore the collection') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                    </a>
                    <a class="encap-catalog-button encap-catalog-button--outline" href="{{ route('contact', ['locale' => $locale]) }}">
                        {{ __('Contact our team') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                    </a>
                </div>
                <p class="encap-catalog-hero-footnote"><span aria-hidden="true"></span>{{ __('Company information is maintained by the ENCAP team.') }}</p>
            </div>
            <div class="encap-catalog-hero-visual" aria-hidden="true">
                <div class="encap-catalog-orbit encap-catalog-orbit--outer"></div>
                <div class="encap-catalog-orbit encap-catalog-orbit--middle"></div>
                <div class="encap-catalog-orbit encap-catalog-orbit--inner"></div>
                <figure class="encap-catalog-hero-image encap-catalog-hero-image--primary">
                    <img src="{{ \App\Support\EncapImage::url('farm-cultivation-2026.jpg', 'https://images.unsplash.com/photo-1719757168062-3ceb06988532?fit=crop&fm=jpg&w=1400&q=85') }}" alt="" width="900" height="1100" fetchpriority="high" decoding="async">
                    <figcaption>{{ __('Agricultural production') }}</figcaption>
                </figure>
                <figure class="encap-catalog-hero-image encap-catalog-hero-image--secondary">
                    <img src="{{ \App\Support\EncapImage::url('produce-mix-2026.jpg', 'https://images.unsplash.com/photo-1764390745408-74f0dbbb5bca?fit=crop&fm=jpg&w=800&q=85') }}" alt="" width="800" height="900" loading="lazy" decoding="async">
                    <figcaption>{{ __('Connected supply') }}</figcaption>
                </figure>
                <figure class="encap-catalog-hero-image encap-catalog-hero-image--tertiary">
                    <img src="{{ \App\Support\EncapImage::url('cargo-port-2026.jpg', 'https://images.unsplash.com/photo-1759272840538-ae4b07214c71?fit=crop&fm=jpg&w=1000&q=85') }}" alt="" width="650" height="520" loading="lazy" decoding="async">
                    <figcaption>{{ __('Worldwide') }}</figcaption>
                </figure>
            </div>
        </div>
    </div>
</section>

<section class="encap-catalog-summary-band" aria-label="{{ __('Group Companies') }}">
    <div class="container encap-catalog-summary-grid">
        <div class="encap-catalog-summary-count">
            <strong>{{ number_format($companies->total()) }}</strong>
            <span>{{ __('Published companies') }}</span>
        </div>
        <div class="encap-catalog-summary-item"><span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-network"></use></svg></span><span>{{ __('Group Companies') }}</span></div>
        <div class="encap-catalog-summary-item"><span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-globe"></use></svg></span><span>{{ __('Global markets') }}</span></div>
        <div class="encap-catalog-summary-item"><span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-compass"></use></svg></span><span>{{ __('Regional presence') }}</span></div>
    </div>
</section>

<section class="encap-catalog-section" id="encap-companies-grid" aria-labelledby="encap-companies-section-title">
    <div class="container">
        <div class="encap-catalog-section-heading">
            <div class="encap-catalog-heading">
                <span class="encap-catalog-kicker">{{ __('Explore the range') }} <i aria-hidden="true"></i></span>
                <h2 id="encap-companies-section-title">{{ __('Meet our group companies.') }}</h2>
                <p>{{ __('Choose a company to learn more about its market and location, or contact our team for business enquiries.') }}</p>
            </div>
            <div class="encap-catalog-heading-aside"><p>{{ __('The public directory shows published company information only.') }}</p></div>
        </div>

        @if($companies->isEmpty())
            <div class="encap-catalog-empty">
                <div class="encap-catalog-empty-art" aria-hidden="true"><svg viewBox="0 0 100 100"><rect x="18" y="18" width="64" height="64" rx="14"></rect><path d="M32 68V40h36v28M42 40V30h16v10M42 50h5M53 50h5M42 60h5M53 60h5"></path></svg></div>
                <h3>{{ __('No companies have been published yet.') }}</h3>
                <p>{{ __('Contact our team for business and export enquiries.') }}</p>
                <a class="encap-catalog-button encap-catalog-button--dark" href="{{ route('contact', ['locale' => $locale]) }}">{{ __('Contact our team') }} <span aria-hidden="true">↗</span></a>
            </div>
        @else
            <div class="encap-product-grid">
                @foreach($companies as $index => $company)
                    @php($companyName = $locale === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en)
                    @php($companySummary = $locale === 'ar' && $company->short_description_ar ? $company->short_description_ar : ($company->short_description_en ?? ''))
                    @php($companyImage = $company->image ?: 'farm-cultivation-2026.jpg')
                    <a class="encap-product-card encap-company-card" href="{{ route('companies.show', ['locale' => $locale, 'company' => $company->slug]) }}">
                        <div class="encap-product-image">
                            <span class="encap-product-count">{{ str_pad((string) (($companies->firstItem() ?? 1) + $index), 2, '0', STR_PAD_LEFT) }}</span>
                            <img src="{{ \App\Support\EncapImage::url($companyImage, 'https://images.unsplash.com/photo-1719757168062-3ceb06988532?fit=crop&fm=jpg&w=1000&q=85') }}" alt="{{ $companyName }}" loading="lazy" width="700" height="850" decoding="async">
                            <span class="encap-product-open">↗</span>
                        </div>
                        <div class="encap-product-info">
                            <div>
                                <h3>{{ $companyName }}</h3>
                                <p>{{ $companySummary }}</p>
                                @if($company->country_en || $company->country_ar)
                                    <small class="encap-company-location">{{ $locale === 'ar' && $company->country_ar ? $company->country_ar : ($company->country_en ?? '') }}@if($company->location_en || $company->location_ar) <span aria-hidden="true">·</span> {{ $locale === 'ar' && $company->location_ar ? $company->location_ar : ($company->location_en ?? '') }}@endif</small>
                                @endif
                            </div>
                            <span class="encap-product-link">{{ __('View company') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($companies->hasPages())<div class="pagination">{{ $companies->links() }}</div>@endif
        @endif
    </div>
</section>
@endsection
