@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@php($companyName = $locale === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en)
@php($shortDescription = $locale === 'ar' && $company->short_description_ar ? $company->short_description_ar : ($company->short_description_en ?? ''))
@php($description = $locale === 'ar' && $company->description_ar ? $company->description_ar : ($company->description_en ?? ''))
@php($country = $locale === 'ar' && $company->country_ar ? $company->country_ar : ($company->country_en ?? ''))
@php($location = $locale === 'ar' && $company->location_ar ? $company->location_ar : ($company->location_en ?? ''))
@section('content')
<section class="page-head">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('companies', ['locale' => $locale]) }}">{{ __('Our Companies') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $companyName }}</span>
        </nav>
        <div class="page-intro-grid">
            <div>
                <div class="eyebrow"><img class="encap-inline-brand" src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="ENCAP" width="22" height="22" decoding="async"> {{ __('ENCAP subsidiary') }}</div>
                <h1>{{ $companyName }}</h1>
                @if(filled($shortDescription))<p class="lead">{{ $shortDescription }}</p>@endif
            </div>
            <aside class="page-aside-note">
                <strong>{{ $country }}</strong>
                @if(filled($location))<span>{{ $location }}</span>@endif
                @if($company->website_url)<a href="{{ $company->website_url }}" target="_blank" rel="noopener noreferrer">{{ __('Company website') }} ↗</a>@endif
            </aside>
        </div>
    </div>
</section>

<section class="encap-company-detail-section">
    <div class="container product-detail">
        <div class="product-detail-media">
            @php($mainCompanyImage = $companyImages[0] ?? null)
            <div class="product-gallery-main" data-gallery-viewer>
                @if($mainCompanyImage)
                    <img data-gallery-main src="{{ $mainCompanyImage['url'] }}" alt="{{ $companyName }}" width="900" height="760" fetchpriority="high" decoding="async">
                    @if(count($companyImages) > 1)<span class="product-gallery-counter" data-gallery-counter data-total-images="{{ count($companyImages) }}">1 / {{ count($companyImages) }}</span>@endif
                @else
                    <div class="product-visual" role="img" aria-label="{{ $companyName }}"></div>
                @endif
            </div>
            @if(count($companyImages) > 1)
                <div class="product-gallery-meta">
                    <div><span>{{ __('Company photography') }}</span><strong>{{ __('Explore the company gallery') }}</strong></div>
                    <span class="product-gallery-total">{{ count($companyImages) }} {{ __('images') }}</span>
                </div>
                <div class="product-gallery-thumbs" role="group" aria-label="{{ __('Company gallery') }}">
                    @foreach($companyImages as $image)
                        <button class="product-gallery-thumb" type="button" data-gallery-thumb data-gallery-index="{{ $loop->iteration }}" data-image-src="{{ $image['url'] }}" data-image-alt="{{ $companyName }}" aria-label="{{ __('View company image') }} {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                            <img src="{{ $image['url'] }}" alt="" loading="lazy" width="240" height="190" decoding="async">
                            <span class="product-gallery-thumb-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="product-gallery-hint">{{ __('Select an image to view it larger.') }}</p>
            @endif
        </div>

        <div class="product-detail-copy">
            <div class="product-overview">
                <div class="eyebrow">{{ __('Company overview') }}</div>
                <h2>{{ $companyName }}</h2>
                <p>{{ $description ?: $shortDescription }}</p>
            </div>
            @if(filled($country) || filled($location))
                <div class="detail-block">
                    <h3 class="detail-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-compass"></use></svg>{{ __('Company location') }}</h3>
                    @if(filled($country))<p>{{ $country }}</p>@endif
                    @if(filled($location))<p>{{ $location }}</p>@endif
                    @if($company->slug === 'hadaeq-al-shamal')
                        <a href="https://www.google.com/maps/search/?api=1&amp;query=Aljouf+Saudi+Arabia" target="_blank" rel="noopener noreferrer">{{ __('View location on map') }} ↗</a>
                    @endif
                </div>
            @endif
            <div class="detail-block">
                <h3 class="detail-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-network"></use></svg>{{ __('Part of the ENCAP group') }}</h3>
                <p>{{ __('A regional company within the ENCAP group.') }}</p>
            </div>
            <a class="button" href="{{ route('contact', ['locale' => $locale]) }}">{{ __('Contact our team') }} <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>
@endsection
