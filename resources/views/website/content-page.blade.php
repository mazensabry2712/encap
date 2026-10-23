@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('content')
@if($page === 'about')
<section class="encap-about-hero" aria-labelledby="encap-about-title">
    <div class="encap-about-hero-orbit encap-about-hero-orbit--one" aria-hidden="true"></div>
    <div class="encap-about-hero-orbit encap-about-hero-orbit--two" aria-hidden="true"></div>
    <div class="container encap-about-hero-container">
        <nav class="breadcrumbs encap-about-breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a><span aria-hidden="true">/</span><span aria-current="page">{{ $pageContent['eyebrow'] }}</span>
        </nav>
        <div class="encap-about-hero-grid">
            <div class="encap-about-hero-copy">
                <span class="encap-about-kicker"><span aria-hidden="true"></span>{{ $pageContent['eyebrow'] }}</span>
                <h1 id="encap-about-title">{{ $pageContent['title'] }}</h1>
                <p class="encap-about-hero-intro">{{ $pageContent['intro'] }}</p>
                <div class="encap-about-hero-actions">
                    <a class="encap-button encap-button-primary" href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Explore products') }} <span aria-hidden="true">↗</span></a>
                    <a class="encap-button encap-button-quiet" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request a Quote') }} <span aria-hidden="true">↗</span></a>
                </div>
                <div class="encap-about-hero-meta"><span>{{ __('Agricultural production') }}</span><i aria-hidden="true"></i><span>{{ __('Sourcing') }}</span><i aria-hidden="true"></i><span>{{ __('Processing') }}</span><i aria-hidden="true"></i><span>{{ __('Export') }}</span></div>
            </div>
            <div class="encap-about-hero-art" aria-label="{{ $pageContent['hero_badge'] }}">
                <div class="encap-about-art-orbit" aria-hidden="true"></div>
                <figure class="encap-about-landscape">
                    <img src="{{ \App\Support\EncapImage::url('farm-landscape.jpg', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1500&q=82') }}" alt="{{ $pageContent['hero_landscape_alt'] }}" width="1200" height="1450" fetchpriority="high" decoding="async">
                    <figcaption>{{ $pageContent['hero_caption'] }}</figcaption>
                </figure>
                <figure class="encap-about-produce">
                    <img src="{{ \App\Support\EncapImage::url('fresh-produce.jpg', 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=82') }}" alt="{{ $pageContent['hero_produce_alt'] }}" width="700" height="520" loading="lazy" decoding="async">
                    <figcaption><span>ENCAP /</span> {{ $pageContent['hero_badge'] }}</figcaption>
                </figure>
                <div class="encap-about-floating-mark" aria-hidden="true"><img src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" width="32" height="32" decoding="async"></div>
            </div>
        </div>
        <div class="encap-about-hero-baseline"><span>{{ __('From origin to market') }}</span><span aria-hidden="true">✳</span><span>{{ __('Growing with purpose. Supplying with care.') }}</span></div>
    </div>
</section>
@else
<section class="page-head"><div class="container"><div class="breadcrumbs"><a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a> &nbsp; / &nbsp; {{ $pageContent['eyebrow'] }}</div><div class="page-intro-grid"><div><div class="eyebrow">{{ $pageContent['eyebrow'] }}</div><h1>{{ $pageContent['title'] }}</h1><p class="lead">{{ $pageContent['intro'] }}</p></div><aside class="page-aside-note"><strong><img class="encap-inline-brand" src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="ENCAP" width="24" height="24" decoding="async"> {{ __('Our approach') }}</strong>{{ __('Clear product requirements, current availability and direct communication with our team.') }}</aside></div></div></section>
@endif
@if(!empty($pageContent['hero_image']))
<section class="encap-service-visual-section">
    <div class="container encap-service-visual">
        <figure class="encap-service-visual-figure">
            <img src="{{ $pageContent['hero_image'] }}" alt="{{ $pageContent['hero_image_alt'] }}" width="1400" height="1050" fetchpriority="high" decoding="async">
            
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
@if($page !== 'about')
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
                        @unless(in_array($page, ['farms', 'packing-stations', 'export'], true))
                            <span class="encap-service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        @endunless
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
                        <li>@unless(in_array($page, ['farms', 'packing-stations', 'export'], true))<span class="encap-service-step-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>@endunless<div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></li>
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

@if(empty($pageContent['services']))
<div class="content-story-marker"><span class="content-story-brand"><img src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="ENCAP" width="24" height="24" decoding="async"></span><span>{{ __('From origin to market') }}</span></div><div class="content-layout"><div class="content-copy"><p>{{ $pageContent['body'] }}</p></div><aside class="feature-panel"><div class="eyebrow">{{ __('Let us talk supply') }}</div><strong>{{ __('Looking for a reliable agricultural supply partner?') }}</strong><p>{{ __('Share your product, quantity and destination requirements with our team.') }}</p><a class="text-link" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request a Quote') }} <span>↗</span></a></aside></div>
@endif
</div></section>
@endif

@if($page === 'about')
<section class="encap-about-journey" aria-labelledby="encap-about-journey-title">
    <div class="container">
        <div class="encap-about-section-heading encap-about-journey-heading"><div><span class="encap-section-index">{{ __('The ENCAP approach') }}</span><h2 id="encap-about-journey-title">{{ $pageContent['journey_heading'] }}</h2></div><p>{{ $pageContent['journey_intro'] }}</p></div>
        <div class="encap-about-journey-grid">
            @foreach($pageContent['journey'] as $stage)
                <article class="encap-about-journey-card">
                    <div class="encap-about-journey-card-top"><span class="encap-about-journey-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-{{ $stage['icon'] }}"></use></svg></span><span class="encap-about-journey-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
                    <span class="encap-about-journey-eyebrow">{{ $stage['eyebrow'] }}</span><h3>{{ $stage['title'] }}</h3><p>{{ $stage['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="encap-about-practice" aria-labelledby="encap-about-practice-title">
    <div class="container encap-about-practice-grid">
        <figure class="encap-about-practice-visual">
            <img src="{{ \App\Support\EncapImage::url('cultivated-field.jpg', 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=82') }}" alt="{{ $pageContent['approach_image_alt'] }}" width="1200" height="1000" loading="lazy" decoding="async">
            <figcaption>{{ $pageContent['hero_caption'] }}</figcaption>
            <span class="encap-about-practice-stamp" aria-hidden="true"><img src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" width="38" height="38" loading="lazy" decoding="async"><span>ORIGIN<br>TO MARKET</span></span>
        </figure>
        <div class="encap-about-practice-copy">
            <span class="encap-section-index">{{ __('How we work') }}</span><h2 id="encap-about-practice-title">{{ $pageContent['approach_heading'] }}</h2>
            <p class="encap-about-practice-body">{{ $pageContent['body'] }}</p><p class="encap-about-practice-intro">{{ $pageContent['approach_intro'] }}</p>
            <div class="encap-about-practice-points">
                @foreach($pageContent['approach_points'] as $point)
                    <article class="encap-about-practice-point"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $point['title'] }}</h3><p>{{ $point['description'] }}</p></div></article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="encap-about-principles" aria-labelledby="encap-about-principles-heading">
    <div class="container">
        <div class="encap-about-principles-heading"><span class="encap-section-index">{{ __('Company direction') }}</span><h2 id="encap-about-principles-heading">{{ __('Our mission, vision & values') }}</h2><p>{{ __('A practical approach to connecting Egyptian agricultural production with global buyers.') }}</p></div>
        <div class="encap-principles-grid">
            <article class="encap-principle-card"><div class="encap-principle-card-top"><span class="encap-principle-index">01</span><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-sprout"></use></svg></div><h3>{{ __('Our mission') }}</h3><p>{{ __('Connect Egyptian agricultural production with buyers through clear supply planning and export coordination.') }}</p></article>
            <article class="encap-principle-card"><div class="encap-principle-card-top"><span class="encap-principle-index">02</span><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-globe"></use></svg></div><h3>{{ __('Our vision') }}</h3><p>{{ __('Build long-term supply partnerships that help Egyptian produce reach suitable global markets.') }}</p></article>
            <article class="encap-principle-card"><div class="encap-principle-card-top"><span class="encap-principle-index">03</span><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-shield-check"></use></svg></div><h3>{{ __('Our values') }}</h3><p>{{ __('Clear communication, responsible sourcing and product specifications agreed before commitment guide our approach.') }}</p></article>
        </div>
    </div>
    <div class="encap-about-principles-orbit encap-about-principles-orbit--one" aria-hidden="true"></div><div class="encap-about-principles-orbit encap-about-principles-orbit--two" aria-hidden="true"></div>
</section>

<section class="encap-about-catalog" aria-labelledby="encap-about-catalog-title">
    <div class="container">
        <div class="encap-about-section-heading encap-about-catalog-heading"><div><span class="encap-section-index">{{ __('A closer look at our catalogue') }}</span><h2 id="encap-about-catalog-title">{{ __('Produce for different market needs.') }}</h2></div><p>{{ __('Explore our product categories, then share your required form, quantity and destination so availability can be confirmed for your enquiry.') }}</p></div>
        @if($categories->isNotEmpty())
            <div class="encap-about-catalog-grid">
                @foreach($categories as $category)
                    <a class="encap-about-category-card" href="{{ route('products.show', ['locale' => $locale, 'category' => $category->slug]) }}">
                        <div class="encap-about-category-media"><img src="{{ \App\Support\EncapImage::url($category->image) }}" alt="{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}" width="640" height="720" loading="lazy" decoding="async"><span aria-hidden="true">↗</span></div>
                        <div class="encap-about-category-copy"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}</h3></div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="encap-about-catalog-empty"><p>{{ __('Browse the ENCAP catalogue to explore the product categories currently listed.') }}</p><a class="encap-button encap-button-primary" href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Explore products') }} <span aria-hidden="true">↗</span></a></div>
        @endif
        <div class="encap-about-catalog-footer"><span>{{ __('Availability and specifications are confirmed for each enquiry.') }}</span><a href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('View all products') }} <span aria-hidden="true">↗</span></a></div>
    </div>
</section>

<section class="encap-about-final-cta" aria-labelledby="encap-about-final-title"><div class="container">
    <div class="encap-about-final-panel encap-final-panel">
        <div class="encap-about-final-orbit" aria-hidden="true"><div class="encap-shared-brand-core"><img src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" width="45" height="45" loading="lazy" decoding="async"><span>ENCAP</span><small>{{ __('Contact ENCAP directly') }}</small></div><span class="encap-about-final-orbit-sweep"></span></div>
        <div class="encap-about-final-copy"><span class="encap-section-index">{{ __('For importers, distributors and partners') }}</span><h2 id="encap-about-final-title">{{ __('Let’s talk about what your market needs.') }}</h2><p>{{ __('Share the product, quantity and destination you have in mind. We will start by reviewing the details that matter to your enquiry.') }}</p><a class="encap-button encap-button-light" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Start an export enquiry') }} <span aria-hidden="true">↗</span></a></div>
        <span class="encap-about-final-index" aria-hidden="true">ENCAP <i>/ 04</i></span>
    </div>
</div></section>
@endif
@endsection