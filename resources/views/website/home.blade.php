@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('content')
<section class="encap-hero-experience encap-hero" data-encap-hero aria-labelledby="encap-hero-title">
 <div class="encap-hero-background" aria-hidden="true">
  <div class="encap-hero-image-plane" data-hero-layer data-hero-depth="7" data-hero-background>
   <img class="encap-hero-image" src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&amp;fit=crop&amp;w=2000&amp;q=82" alt="" width="2400" height="1600" fetchpriority="high" decoding="async">
  </div>
  <div class="encap-hero-vignette"></div>
  <div class="encap-hero-backdrop-orbit encap-hero-backdrop-orbit--one"></div>
  <div class="encap-hero-backdrop-orbit encap-hero-backdrop-orbit--two"></div>
 </div>
 <div class="container encap-hero-experience-grid">
  <div class="encap-hero-copy">
   <div class="encap-kicker encap-hero-kicker" data-hero-reveal style="--hero-delay:80ms"><span class="encap-kicker-dot"></span><span>{{ __('Egyptian agricultural production & export') }}</span></div>
   <h1 id="encap-hero-title" data-hero-reveal style="--hero-delay:160ms">{{ __('From Egyptian soil to the world’s table.') }}</h1>
   <p class="encap-hero-lead" data-hero-reveal style="--hero-delay:240ms">{{ __('We bring together our own farms, trusted growers and careful preparation to connect Egyptian produce with global markets.') }}</p>
   <div class="encap-hero-actions" data-hero-reveal style="--hero-delay:320ms">
    <a class="encap-button encap-button-primary" href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Discover our products') }} <span aria-hidden="true">↗</span></a>
    <a class="encap-button encap-button-quiet" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Start a conversation') }} <span aria-hidden="true">→</span></a>
   </div>
   <div class="encap-hero-footnote" data-hero-reveal style="--hero-delay:400ms"><span class="encap-footnote-icon" aria-hidden="true">✳</span><span>{{ __('Production') }} <i>·</i> {{ __('Sourcing') }} <i>·</i> {{ __('Processing') }} <i>·</i> {{ __('Export') }}</span></div>
  </div>
  <div class="encap-hero-stage" data-hero-tilt aria-hidden="true">
   <div class="encap-hero-stage-ring encap-hero-stage-ring--outer"></div>
   <div class="encap-hero-stage-ring encap-hero-stage-ring--inner"></div>
   <div class="encap-hero-orbit-system" data-hero-layer data-hero-depth="44">
    <div class="encap-hero-orbit-track encap-hero-orbit-track--outer">
     <span class="encap-hero-orbit-anchor"><span class="encap-hero-orbit-token encap-hero-orbit-token--production"><span class="encap-hero-orbit-token-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-sprout"></use></svg></span><span class="encap-hero-orbit-token-label">{{ __('Production') }}</span></span></span>
    </div>
    <div class="encap-hero-orbit-track encap-hero-orbit-track--inner">
     <span class="encap-hero-orbit-anchor"><span class="encap-hero-orbit-token encap-hero-orbit-token--export"><span class="encap-hero-orbit-token-icon">↗</span><span class="encap-hero-orbit-token-label">{{ __('Export') }}</span></span></span>
    </div>
   </div>
   <div class="encap-hero-photo-card" data-hero-layer data-hero-depth="24">
    <div class="encap-hero-photo-card-content" data-hero-reveal style="--hero-delay:220ms">
     <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&amp;fit=crop&amp;w=900&amp;q=82" alt="" width="900" height="1120" loading="eager" decoding="async">
     <div class="encap-hero-photo-shade"></div>
     <div class="encap-hero-photo-label">
      <span class="encap-image-label-mark"><img class="encap-image-label-logo" src="{{ asset('assets/favicone.png') }}" alt="" width="28" height="28" decoding="async"></span>
      <span><small>ENCAP / ORIGIN</small><strong>{{ __('Farm to market') }}</strong></span>
     </div>
    </div>
   </div>
   <div class="encap-hero-stamp-layer" data-hero-layer data-hero-depth="38">
    <div class="encap-orbit-stamp encap-hero-motion-stamp"><span>{{ __('Rooted in Egypt') }}</span><b>✳</b><span>{{ __('Connected to opportunity.') }}</span></div>
   </div>
   <div class="encap-hero-floating-layer" data-hero-layer data-hero-depth="42">
    <div class="encap-floating-note encap-hero-floating-note">
     <span class="encap-floating-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-leaf"></use></svg></span>
     <span><strong>{{ __('Farm to market') }}</strong><small>{{ __('Own farms & trusted partners') }}</small></span>
     <span class="encap-floating-arrow">↗</span>
    </div>
   </div>
   <div class="encap-hero-chip-layer" data-hero-layer data-hero-depth="16">
    <div class="encap-hero-mini-chip"><span class="encap-hero-mini-chip-mark">01</span><span><strong>{{ __('One connected journey') }}</strong><small>{{ __('Production') }} · {{ __('Export') }}</small></span></div>
   </div>
  </div>
 </div>
 <div class="container encap-hero-bottom">
  <span class="encap-hero-index"><img src="{{ asset('assets/favicone.png') }}" alt="" width="24" height="24" decoding="async"> / 01</span>
  <span class="encap-hero-bottom-copy">{{ __('Growing with purpose. Supplying with care.') }}</span>
  <a class="encap-hero-scroll-cue" href="#our-approach" aria-label="{{ __('Explore our approach') }}"><span>{{ __('Explore our approach') }}</span><i aria-hidden="true">↓</i></a>
 </div>
</section>
<section class="encap-trust-band"><div class="container encap-trust-grid"><div class="encap-trust-intro"><span class="encap-mini-label">{{ __('One connected journey') }}</span><strong>{{ __('From the field, all the way forward.') }}</strong></div>
 <div class="encap-trust-item"><span class="encap-trust-number">01</span><span class="encap-trust-symbol">↗</span><strong>{{ __('Farm production') }}</strong><small>{{ __('Closer to origin') }}</small></div>
 <div class="encap-trust-item"><span class="encap-trust-number">02</span><span class="encap-trust-symbol">⌘</span><strong>{{ __('Partner sourcing') }}</strong><small>{{ __('A connected network') }}</small></div>
 <div class="encap-trust-item"><span class="encap-trust-number">03</span><span class="encap-trust-symbol">◇</span><strong>{{ __('Product preparation') }}</strong><small>{{ __('Requirements discussed clearly') }}</small></div>
 <div class="encap-trust-item"><span class="encap-trust-number">04</span><span class="encap-trust-symbol">◎</span><strong>{{ __('Export enquiries') }}</strong><small>{{ __('A direct line to our team') }}</small></div>
</div></section>
<section class="encap-service-hub" aria-labelledby="encap-service-hub-title">
 <div class="container">
  <div class="encap-service-hub-heading">
   <div><span class="encap-section-index">{{ __('Three connected capabilities') }}</span><h2 id="encap-service-hub-title">{{ __('From farm management to prepared shipment.') }}</h2></div>
   <p>{{ __('Explore how we connect crop sourcing, post-harvest preparation and export coordination around your requirements.') }}</p>
  </div>
  <div class="encap-service-hub-grid">
   <a class="encap-service-hub-card" href="{{ route('farms', ['locale' => $locale]) }}">
    <span class="encap-service-hub-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-sprout"></use></svg></span>
    <span class="encap-service-hub-number">01 / ORIGIN</span><h3>{{ __('Agriculture & Farms') }}</h3><p>{{ __('Crop sourcing and seasonal planning.') }}</p><span class="encap-service-hub-link">{{ __('Explore service') }} <b>↗</b></span>
   </a>
   <a class="encap-service-hub-card" href="{{ route('packing-stations', ['locale' => $locale]) }}">
    <span class="encap-service-hub-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-package"></use></svg></span>
    <span class="encap-service-hub-number">02 / PREPARATION</span><h3>{{ __('Packing Stations') }}</h3><p>{{ __('Sorting, grading, packing and preparation.') }}</p><span class="encap-service-hub-link">{{ __('Explore service') }} <b>↗</b></span>
   </a>
   <a class="encap-service-hub-card" href="{{ route('export', ['locale' => $locale]) }}">
    <span class="encap-service-hub-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-truck"></use></svg></span>
    <span class="encap-service-hub-number">03 / MARKETS</span><h3>{{ __('Export & Logistics') }}</h3><p>{{ __('Requirements, compliance coordination and shipping partners.') }}</p><span class="encap-service-hub-link">{{ __('Explore service') }} <b>↗</b></span>
   </a>
  </div>
 </div>
</section>
<section class="section encap-story" id="our-approach"><div class="container encap-story-grid">
 <div class="encap-story-art"><img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80" alt="{{ __('Sunlight across agricultural land') }}" loading="lazy" width="900" height="1100"><div class="encap-story-sideword">ORIGIN / QUALITY / GROWTH</div><div class="encap-story-caption"><img class="encap-story-brand" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="36" height="36" loading="lazy" decoding="async"><strong>{{ __('A stronger link between growers and global buyers.') }}</strong></div></div>
 <div class="encap-story-copy"><span class="encap-section-index">{{ __('01 / Who we are') }}</span><div class="encap-kicker">{{ __('Agriculture with a wider view') }}</div><h2>{{ __('Good produce starts with good decisions.') }}</h2><p>{{ __('We work across agricultural production, partner sourcing, processing and export. Our approach starts with understanding the crop, the buyer’s needs and the journey ahead.') }}</p><p>{{ __('Every enquiry is a chance to align product details, availability, preparation and destination before moving forward.') }}</p><a class="encap-text-link" href="{{ route('about', ['locale' => $locale]) }}">{{ __('Discover ENCAP') }} <span>↗</span></a><div class="encap-story-signature"><span class="encap-signature-mark"><img class="encap-signature-logo" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="32" height="32" loading="lazy" decoding="async"></span><span><strong>{{ __('Rooted in Egypt.') }}</strong><small>{{ __('Connected to opportunity.') }}</small></span></div></div>
</div></section>
<section class="section encap-products" id="products"><div class="container">
 <div class="encap-section-heading"><div><span class="encap-section-index">{{ __('02 / Our products') }}</span><div class="encap-kicker">{{ __('The harvest, thoughtfully sourced') }}</div><h2>{{ __('A world of produce. One trusted starting point.') }}</h2></div><div class="encap-heading-aside"><p>{{ __('Explore our agricultural categories and tell us what your market needs. Product form, availability and packaging can be discussed for every enquiry.') }}</p><a class="encap-text-link" href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Explore all products') }} <span>↗</span></a></div></div>
 @if($categories->isNotEmpty())<div class="encap-product-grid">@foreach($categories as $index => $category)<a class="encap-product-card" href="{{ route('products.show', ['locale' => $locale, 'category' => $category->slug]) }}"><div class="encap-product-image"><span class="encap-product-count">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>@if($category->image)<img src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}" loading="lazy" width="700" height="850">@else<div class="encap-product-placeholder"><img class="encap-product-placeholder-logo" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="84" height="84" loading="lazy" decoding="async"></div>@endif<span class="encap-product-open">↗</span></div><div class="encap-product-info"><div><h3>{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}</h3><p>{{ $locale === 'ar' && $category->short_description_ar ? $category->short_description_ar : $category->short_description_en }}</p></div><span class="encap-product-link">{{ __('Explore') }}</span></div></a>@endforeach</div>
 @else<div class="encap-catalog-empty"><span class="encap-kicker">{{ __('Made for your market') }}</span><h3>{{ __('Tell us what you are looking for.') }}</h3><p>{{ __('Our team can discuss sourcing requirements, product forms and export needs directly with you.') }}</p><a class="encap-button encap-button-primary" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request a quote') }} <span>↗</span></a></div>@endif
 <div class="encap-catalog-footer"><span>{{ __('Every market has its own requirements.') }}</span><span>{{ __('Let’s discuss the right fit for yours.') }}</span><a href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Discuss your requirements') }} ↗</a></div>
</div></section>
<section class="section encap-process"><div class="container encap-process-grid"><div class="encap-process-heading"><span class="encap-section-index">{{ __('03 / How we work') }}</span><div class="encap-kicker">{{ __('Clear steps. Direct communication.') }}</div><h2>{{ __('A considered route from field to shipment.') }}</h2><p>{{ __('We keep the process practical: understand your requirement, review sourcing options, align the details and agree on the next step.') }}</p><a class="encap-text-link" href="{{ route('farms', ['locale' => $locale]) }}">{{ __('Explore our production approach') }} <span>↗</span></a><div class="encap-process-decoration"><img class="encap-process-decoration-logo" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="90" height="90" loading="lazy" decoding="async"><span>✳</span></div></div>
 <div class="encap-process-list"><article class="encap-process-row"><span class="encap-process-num">01</span><span class="encap-process-icon"><svg viewBox="0 0 24 24"><use href="#icon-compass"></use></svg></span><div><h3>{{ __('Understand the requirement') }}</h3><p>{{ __('Start with product, quantity, product form and destination.') }}</p></div><span class="encap-process-arrow">↗</span></article><article class="encap-process-row"><span class="encap-process-num">02</span><span class="encap-process-icon"><svg viewBox="0 0 24 24"><use href="#icon-network"></use></svg></span><div><h3>{{ __('Review sourcing and availability') }}</h3><p>{{ __('Review current crop availability and suitable sourcing options.') }}</p></div><span class="encap-process-arrow">↗</span></article><article class="encap-process-row"><span class="encap-process-num">03</span><span class="encap-process-icon"><svg viewBox="0 0 24 24"><use href="#icon-scan"></use></svg></span><div><h3>{{ __('Align quality and packaging') }}</h3><p>{{ __('Discuss specifications, preparation and packaging requirements.') }}</p></div><span class="encap-process-arrow">↗</span></article><article class="encap-process-row"><span class="encap-process-num">04</span><span class="encap-process-icon"><svg viewBox="0 0 24 24"><use href="#icon-truck"></use></svg></span><div><h3>{{ __('Agree on the next step') }}</h3><p>{{ __('Keep communication clear as the enquiry moves forward.') }}</p></div><span class="encap-process-arrow">↗</span></article></div>
</div></section>
<section class="encap-quality-band"><div class="encap-quality-image" aria-hidden="true"></div><div class="container encap-quality-content"><span class="encap-section-index">{{ __('04 / Quality in every detail') }}</span><div class="encap-kicker">{{ __('Care at every stage') }}</div><h2>{{ __('Quality is a journey, not a final checkpoint.') }}</h2><p>{{ __('From sourcing conversations to preparation and delivery planning, the details matter. We work to make those details clear before a commitment is made.') }}</p><a class="encap-button encap-button-light" href="{{ route('quality', ['locale' => $locale]) }}">{{ __('Our approach to quality') }} <span>↗</span></a></div></section>
<section class="section encap-final-cta"><div class="container"><div class="encap-final-panel"><div class="encap-final-orbit" aria-hidden="true"><img class="encap-orbit-brand" src="{{ asset('assets/favicone.png') }}" alt="" aria-hidden="true" width="54" height="54" loading="lazy" decoding="async"><b>✳</b></div><div class="encap-final-copy"><span class="encap-kicker">{{ __('For importers, distributors and partners') }}</span><h2>{{ __('Let’s bring the right produce to your market.') }}</h2><p>{{ __('Share the product, volume and destination you have in mind. We’ll start with the details that matter to your business.') }}</p><a class="encap-button encap-button-light" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Start an export enquiry') }} <span>↗</span></a></div><div class="encap-final-number">05<span> / LET’S TALK</span></div></div></div></section>
@endsection
