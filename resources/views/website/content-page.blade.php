@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('content')
<section class="page-head"><div class="container"><div class="breadcrumbs"><a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a> &nbsp; / &nbsp; {{ $pageContent['eyebrow'] }}</div><div class="page-intro-grid"><div><div class="eyebrow">{{ $pageContent['eyebrow'] }}</div><h1>{{ $pageContent['title'] }}</h1><p class="lead">{{ $pageContent['intro'] }}</p></div><aside class="page-aside-note"><strong><img class="encap-inline-brand" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="24" height="24" decoding="async"> {{ __('Our approach') }}</strong>{{ __('Clear product requirements, current availability and direct communication with our team.') }}</aside></div></div></section>
@if(!empty($pageContent['hero_image']))
<section class="encap-service-visual-section">
    <div class="container encap-service-visual">
        <figure class="encap-service-visual-figure">
            <img src="{{ $pageContent['hero_image'] }}" alt="{{ $pageContent['hero_image_alt'] }}" width="1400" height="1050" fetchpriority="high" decoding="async">
            <figcaption>{{ __('Illustrative stock photography. Actual facilities and service scope are confirmed per enquiry.') }}</figcaption>
        </figure>
        <div class="encap-service-visual-copy">
            <span class="encap-section-index">{{ __('From origin to market') }}</span>
            <h2>{{ $pageContent['visual_heading'] }}</h2>
            <p>{{ $pageContent['body'] }}</p>
            <p>{{ $pageContent['visual_detail'] }}</p>
            <a class="encap-button encap-button-primary" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Discuss your requirements') }} <span>↗</span></a>
        </div>
    </div>
</section>
@endif
<section class="section"><div class="container">
@if(!empty($pageContent['services']))
    <div class="encap-service-detail">
        <div class="encap-service-detail-heading">
            <div><span class="encap-section-index">{{ __('Service overview') }}</span><h2>{{ $pageContent['services_heading'] }}</h2></div>
            <p>{{ $pageContent['services_intro'] }}</p>
        </div>
        <div class="encap-service-grid">
            @foreach($pageContent['services'] as $service)
                <article class="encap-service-card">
                    @if(!empty($service['image']))
                        <div class="encap-service-card-media">
                            <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="720" height="450" loading="lazy" decoding="async">
                        </div>
                    @endif
                    <div class="encap-service-card-content">
                        <span class="encap-service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        @if(!empty($pageContent['workflow']))
            <div class="encap-service-workflow">
                <div class="encap-service-workflow-heading"><span class="encap-section-index">{{ __('How we work') }}</span><h2>{{ $pageContent['workflow_heading'] }}</h2></div>
                <ol class="encap-service-steps">
                    @foreach($pageContent['workflow'] as $step)
                        <li><span class="encap-service-step-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></li>
                    @endforeach
                </ol>
            </div>
        @endif
        <div class="encap-service-cta">
            <div><strong>{{ __('Planning a supply enquiry?') }}</strong><p>{{ __('Share your crop, volume, packing and destination requirements so we can confirm the right next step.') }}</p></div>
            <a class="encap-button encap-button-primary" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request a Quote') }} <span>↗</span></a>
        </div>
        <nav class="encap-service-related" aria-label="{{ __('Related services') }}">
            <a href="{{ route('farms', ['locale' => $locale]) }}">{{ __('Agriculture & Farms') }} <span>↗</span></a>
            <a href="{{ route('packing-stations', ['locale' => $locale]) }}">{{ __('Packing Stations') }} <span>↗</span></a>
            <a href="{{ route('export', ['locale' => $locale]) }}">{{ __('Export & Logistics') }} <span>↗</span></a>
        </nav>
    </div>
@endif
@if($page === 'gallery')
    @if($categories->isNotEmpty())<div class="gallery-grid">@foreach($categories as $category)<figure class="gallery-card" data-tilt><a class="gallery-card-link" href="{{ route('products.show', ['locale' => $locale, 'category' => $category->slug]) }}" aria-label="{{ __('View details') }}: {{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}"><img src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}" loading="lazy" width="600" height="480"><span class="gallery-card-action" aria-hidden="true">↗</span></a><figcaption><span>{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}<small>{{ __('Explore product') }}</small></span></figcaption></figure>@endforeach</div>
    @else<div class="empty-state"><strong>{{ __('Our product range') }}</strong><p>{{ __('Approved farm and product photography will be added to the gallery as it becomes available.') }}</p><a class="button ghost" href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Browse products') }} <span class="button-arrow">↗</span></a></div>@endif
@endif
@if(empty($pageContent['services']))
<div class="content-story-marker"><span class="content-story-brand"><img src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="24" height="24" decoding="async"> / {{ str_pad((array_search($page, ['about', 'farms', 'packing-stations', 'quality', 'export', 'gallery'], true) ?: 0) + 1, 2, '0', STR_PAD_LEFT) }}</span><span>{{ __('From origin to market') }}</span></div><div class="content-layout" style="margin-top:{{ $page === 'gallery' && $categories->isNotEmpty() ? '55px' : '0' }}"><div class="content-copy"><p>{{ $pageContent['body'] }}</p></div><aside class="feature-panel"><div class="eyebrow">{{ __('Let us talk supply') }}</div><strong>{{ __('Looking for a reliable agricultural supply partner?') }}</strong><p>{{ __('Share your product, quantity and destination requirements with our team.') }}</p><a class="text-link" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request a Quote') }} <span>↗</span></a></aside></div>
@endif
@if($page === 'about')
<div class="encap-about-principles" role="region" aria-labelledby="encap-about-principles-heading">
    <div class="encap-about-principles-heading"><span class="encap-section-index">{{ __('Company direction') }}</span><h2 id="encap-about-principles-heading">{{ __('Our mission, vision & values') }}</h2><p>{{ __('A practical approach to connecting Egyptian agricultural production with global buyers.') }}</p></div>
    <div class="encap-principles-grid">
        <article class="encap-principle-card"><span class="encap-principle-index">01</span><h3>{{ __('Our mission') }}</h3><p>{{ __('Connect Egyptian agricultural production with buyers through clear supply planning and export coordination.') }}</p></article>
        <article class="encap-principle-card"><span class="encap-principle-index">02</span><h3>{{ __('Our vision') }}</h3><p>{{ __('Build long-term supply partnerships that help Egyptian produce reach suitable global markets.') }}</p></article>
        <article class="encap-principle-card"><span class="encap-principle-index">03</span><h3>{{ __('Our values') }}</h3><p>{{ __('Clear communication, responsible sourcing and product specifications agreed before commitment guide our approach.') }}</p></article>
    </div>
</div>
@endif
</div></section>
@endsection
