<!doctype html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
@php($seoDefaults = \App\Models\SeoSetting::current())
@php($socialLinks = array_filter([__('Facebook') => $seoDefaults->facebook_url, __('Instagram') => $seoDefaults->instagram_url, __('LinkedIn') => $seoDefaults->linkedin_url, __('YouTube') => $seoDefaults->youtube_url]))
@php($requestPath = request()->path())
@php($pathAfterLocale = preg_replace('/^(en|ar)(?=\/|$)/', '', $requestPath))
@php($localizedSuffix = $pathAfterLocale ? '/' . ltrim($pathAfterLocale, '/') : '')
@php($canonicalBase = rtrim(config('app.url'), '/'))
@php($canonicalUrl = $canonicalBase . '/' . ($locale ?? 'en') . $localizedSuffix)
@php($alternateEnglishUrl = $canonicalBase . '/en' . $localizedSuffix)
@php($alternateArabicUrl = $canonicalBase . '/ar' . $localizedSuffix)
@php($defaultTitle = ($locale ?? 'en') === 'ar' ? $seoDefaults->site_title_ar : $seoDefaults->site_title_en)
@php($defaultDescription = ($locale ?? 'en') === 'ar' ? $seoDefaults->site_description_ar : $seoDefaults->site_description_en)
@php($pageTitle = $pageSeo['title'] ?? $defaultTitle)
@php($pageDescription = $pageSeo['description'] ?? $defaultDescription)
@php($robotsDirective = $pageSeo['robots'] ?? $seoDefaults->default_robots)
@php($ogImageUrl = $seoDefaults->default_og_image
    ? \App\Support\EncapImage::url($seoDefaults->default_og_image)
    : \App\Support\EncapImage::url('farm-landscape.jpg', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?fit=crop&fm=jpg&w=1200&q=82'))
@php($organizationSchema = ['@context' => 'https://schema.org', '@graph' => [['@type' => 'Organization', 'name' => $seoDefaults->organization_name, 'url' => $seoDefaults->organization_url ?: $canonicalBase], ['@type' => 'WebSite', 'name' => $seoDefaults->organization_name, 'url' => $canonicalBase, 'inLanguage' => $locale ?? 'en']]])
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="color-scheme" content="light"><link rel="icon" type="image/png" href="{{ \App\Support\EncapImage::url('favicon.png') }}"><link rel="apple-touch-icon" href="{{ \App\Support\EncapImage::url('favicon.png') }}"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ $pageTitle }}</title><meta name="description" content="{{ $pageDescription }}"><meta name="robots" content="{{ $robotsDirective }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if(!isset($category) || ($category->name_ar && $category->description_ar))
        <link rel="alternate" hreflang="en" href="{{ $alternateEnglishUrl }}"><link rel="alternate" hreflang="ar" href="{{ $alternateArabicUrl }}"><link rel="alternate" hreflang="x-default" href="{{ $alternateEnglishUrl }}">
    @endif
    <meta property="og:type" content="website"><meta property="og:site_name" content="{{ $seoDefaults->organization_name }}"><meta property="og:title" content="{{ $pageTitle }}"><meta property="og:description" content="{{ $pageDescription }}"><meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta name="twitter:card" content="summary_large_image"><meta name="theme-color" content="#08291e">
    @if(request()->routeIs('home'))
        <link rel="preload" as="image" href="{{ \App\Support\EncapImage::url('farm-landscape.jpg', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=2000&q=82') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/website.css') }}">
    <link rel="stylesheet" href="{{ asset('css/encap-redesign.css') }}?v=20261023-product-cta-centering-fix-1">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    @if(request()->routeIs('home'))<link rel="stylesheet" href="{{ asset('css/hero-experience.css') }}">@endif
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @stack('head')
</head>
<body class="site-body">
<svg xmlns="http://www.w3.org/2000/svg" class="encap-icon-sprite" aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">
    <symbol id="icon-sprout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-8"/><path d="M12 13C5 13 3 9 3 4c6 0 9 3 9 9Z"/><path d="M12 16c0-6 4-10 9-10 0 6-3 10-9 10Z"/></symbol>
    <symbol id="icon-network" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="18" r="2.5"/><circle cx="19" cy="18" r="2.5"/><path d="m10.8 7.2-4.5 8.6M13.2 7.2l4.5 8.6M7.7 18h8.6"/></symbol>
    <symbol id="icon-package" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5"/></symbol>
    <symbol id="icon-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></symbol>
    <symbol id="icon-leaf" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 3.5C10 3 4 7.5 4 14.5A6 6 0 0 0 10 20c7 0 11-7.5 10.5-16.5Z"/><path d="M3 21c3.5-5 7-8 13-11"/></symbol>
    <symbol id="icon-shield-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m8.5 12 2.3 2.3 4.8-5"/></symbol>
    <symbol id="icon-compass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m15.7 8.3-2.4 5-5 2.4 2.4-5 5-2.4Z"/></symbol>
    <symbol id="icon-arrow-up-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M7 7h10v10"/></symbol>
    <symbol id="icon-truck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v12H3zM14 10h4l3 3v5h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></symbol>
    <symbol id="icon-scan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M3 16v3a2 2 0 0 0 2 2h3"/><path d="M7 12h10M12 7v10"/></symbol>
</svg>
<a class="skip-link" href="#main-content">{{ __('Skip to content') }}</a>
@php($currentLocale = $locale ?? 'en')
<header class="site-header primary-header">
    <div class="container primary-nav">
        <a class="primary-brand" href="{{ route('home', ['locale' => $currentLocale]) }}" aria-label="ENCAP — home">
            <img class="primary-brand-logo" src="{{ \App\Support\EncapImage::url('logo.png') }}" alt="ENCAP — Agriculture and Export" width="300" height="90" decoding="async">
        </a>
        @php($operationsActive = request()->routeIs('farms', 'packing-stations', 'export', 'quality'))
        <nav class="primary-links" aria-label="{{ __('Main navigation') }}">
            <a href="{{ route('home', ['locale' => $currentLocale]) }}" @if(request()->routeIs('home')) aria-current="page" @endif>{{ __('Home') }}</a>
            <a href="{{ route('about', ['locale' => $currentLocale]) }}" @if(request()->routeIs('about')) aria-current="page" @endif>{{ __('About') }}</a>
            <details class="primary-dropdown">
                <summary @if($operationsActive) class="is-active" @endif><span>{{ $currentLocale === 'ar' ? 'عملياتنا' : 'Operations' }}</span><svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 6 5 5 5-5"/></svg></summary>
                <div class="primary-dropdown-panel">
                    <a href="{{ route('farms', ['locale' => $currentLocale]) }}" @if(request()->routeIs('farms')) aria-current="page" @endif>{{ __('Agriculture & Farms') }}</a>
                    <a href="{{ route('packing-stations', ['locale' => $currentLocale]) }}" @if(request()->routeIs('packing-stations')) aria-current="page" @endif>{{ __('Packing Stations') }}</a>
                    <a href="{{ route('export', ['locale' => $currentLocale]) }}" @if(request()->routeIs('export')) aria-current="page" @endif>{{ __('Export & Logistics') }}</a>
                    <a href="{{ route('quality', ['locale' => $currentLocale]) }}" @if(request()->routeIs('quality')) aria-current="page" @endif>{{ __('Quality') }}</a>
                    
                </div>
            </details>
            <a href="{{ route('products.index', ['locale' => $currentLocale]) }}" @if(request()->routeIs('products.*')) aria-current="page" @endif>{{ __('Products') }}</a>
            <a href="{{ route('contact', ['locale' => $currentLocale]) }}" @if(request()->routeIs('contact')) aria-current="page" @endif>{{ __('Contact') }}</a>
        </nav>
        <div class="primary-actions">
            <div class="language-switcher" data-language-switcher>
                <button class="language-switcher-trigger" type="button" aria-label="{{ __('Select language') }}" title="{{ __('Select language') }}" aria-expanded="false" aria-controls="language-menu" data-language-trigger>
                    <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path>
                    </svg>
                    <span class="language-trigger-indicator" aria-hidden="true"></span>
                </button>
                <nav class="language-menu" id="language-menu" aria-label="{{ __('Select language') }}" data-language-menu hidden>
                    <p class="language-menu-label">{{ __('Select language') }}</p>
                    <a class="language-option" href="{{ $alternateEnglishUrl }}" lang="en" @if($currentLocale === 'en') aria-current="page" @endif>
                        <span class="language-option-code">EN</span>
                        <span class="language-option-text"><strong>English</strong><small>English</small></span>
                        @if($currentLocale === 'en')<svg class="language-option-check" viewBox="0 0 20 20" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4 4 8-8"></path></svg>@endif
                    </a>
                    <a class="language-option" href="{{ $alternateArabicUrl }}" lang="ar" @if($currentLocale === 'ar') aria-current="page" @endif>
                        <span class="language-option-code">AR</span>
                        <span class="language-option-text" dir="rtl"><strong>العربية</strong><small>اللغة العربية</small></span>
                        @if($currentLocale === 'ar')<svg class="language-option-check" viewBox="0 0 20 20" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10 4 4 8-8"></path></svg>@endif
                    </a>
                </nav>
            </div>
            <a class="button primary-quote" href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }} <span class="button-arrow" aria-hidden="true">↗</span></a>
            <details class="primary-mobile-menu">
                <summary aria-label="{{ __('Open navigation') }}" data-primary-menu-trigger><span class="primary-menu-word">{{ __('Menu') }}</span><span class="primary-menu-icon" aria-hidden="true"><i></i><i></i><i></i></span></summary>
                <nav class="primary-mobile-panel" aria-label="{{ __('Mobile navigation') }}" data-primary-mobile-nav>
                    <div class="primary-mobile-panel-links">
                    <a href="{{ route('home', ['locale' => $currentLocale]) }}" @if(request()->routeIs('home')) aria-current="page" @endif>{{ __('Home') }}</a>
                    <a href="{{ route('about', ['locale' => $currentLocale]) }}" @if(request()->routeIs('about')) aria-current="page" @endif>{{ __('About') }}</a>
                    <a href="{{ route('farms', ['locale' => $currentLocale]) }}" @if(request()->routeIs('farms')) aria-current="page" @endif>{{ __('Agriculture & Farms') }}</a>
                    <a href="{{ route('packing-stations', ['locale' => $currentLocale]) }}" @if(request()->routeIs('packing-stations')) aria-current="page" @endif>{{ __('Packing Stations') }}</a>
                    <a href="{{ route('export', ['locale' => $currentLocale]) }}" @if(request()->routeIs('export')) aria-current="page" @endif>{{ __('Export & Logistics') }}</a>
                    <a href="{{ route('products.index', ['locale' => $currentLocale]) }}" @if(request()->routeIs('products.*')) aria-current="page" @endif>{{ __('Products') }}</a>
                    <a href="{{ route('quality', ['locale' => $currentLocale]) }}" @if(request()->routeIs('quality')) aria-current="page" @endif>{{ __('Quality') }}</a>
                    
                    <a href="{{ route('contact', ['locale' => $currentLocale]) }}" @if(request()->routeIs('contact')) aria-current="page" @endif>{{ __('Contact') }}</a>
                    </div>
                    <a class="primary-mobile-quote" href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }} <span aria-hidden="true">↗</span></a>
                </nav>
            </details>
        </div>
    </div>
</header>
<main id="main-content">@yield('content')</main>
<footer class="footer"><div class="container"><div class="footer-top">
    <div><a class="brand" href="{{ route('home', ['locale' => $currentLocale]) }}" aria-label="ENCAP — home"><img class="brand-logo" src="{{ \App\Support\EncapImage::url('logo.png') }}" alt="ENCAP — Agriculture and Export" width="250" height="72" loading="lazy" decoding="async"></a><p class="footer-intro">{{ __('From Egyptian agricultural production and trusted partner farms to global markets.') }}</p>
    @if(!empty($socialLinks))<nav class="footer-social-links" aria-label="{{ __('Social profiles') }}">@foreach($socialLinks as $network => $url)<a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $network }} <span aria-hidden="true">↗</span></a>@endforeach</nav>@endif
    </div>
    <div><h2 class="footer-heading">{{ __('Explore') }}</h2><div class="footer-links"><a href="{{ route('about', ['locale' => $currentLocale]) }}">{{ __('About') }}</a><a href="{{ route('farms', ['locale' => $currentLocale]) }}">{{ __('Agriculture & Farms') }}</a><a href="{{ route('packing-stations', ['locale' => $currentLocale]) }}">{{ __('Packing Stations') }}</a><a href="{{ route('quality', ['locale' => $currentLocale]) }}">{{ __('Quality') }}</a><a href="{{ route('export', ['locale' => $currentLocale]) }}">{{ __('Export & Logistics') }}</a></div></div>
    <div><h2 class="footer-heading">{{ __('Products & contact') }}</h2><div class="footer-links"><a href="{{ route('products.index', ['locale' => $currentLocale]) }}">{{ __('Our Products') }}</a><a href="{{ route('contact', ['locale' => $currentLocale]) }}">{{ __('Contact') }}</a><a href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }}</a></div></div>
</div>
<section class="footer-business-contacts" aria-labelledby="footer-business-title">
 <div class="footer-business-heading"><div><span class="footer-business-eyebrow">🇸🇦 🇪🇬 ENCAP GROUP</span><h2 id="footer-business-title">{{ __('Business Inquiries') }}</h2></div><p>{{ __('Contact our regional teams for business and export enquiries.') }}</p></div>
 <div class="footer-subsidiary"><span class="footer-subsidiary-flag" aria-hidden="true">🇸🇦</span><div><span class="footer-subsidiary-label">{{ __('ENCAP subsidiary') }}</span><h3>{{ $currentLocale === 'ar' ? 'شركة حدائق الشمال' : 'Hadaeq Al-Shamal Company' }} @if($currentLocale !== 'ar')<span lang="ar" dir="rtl">شركة حدائق الشمال</span>@endif</h3><p>🇸🇦 {{ __('Kingdom of Saudi Arabia') }}</p><a href="https://www.google.com/maps/search/?api=1&query=Tabarjal+Al-Aziziyah+Al-Jawf+Saudi+Arabia" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Open location in Maps') }}">📍 {{ __('Al-Jawf region — Tabarjal — Al-Aziziyah neighborhood') }} <span aria-hidden="true">↗</span></a></div></div>
 <div class="footer-contact-grid">
  <article class="footer-contact-card"><div class="footer-contact-avatar" aria-hidden="true">AS</div><div class="footer-contact-content"><h3>🇸🇦 Eng. Ahmed Elshamy</h3><p class="footer-contact-role">{{ __('Chairman of the Board') }}</p><a class="footer-contact-phone" href="tel:+966568309656">📞 <span dir="ltr">+966 56 830 9656</span></a><a class="footer-contact-location" href="https://www.google.com/maps/search/?api=1&query=Tabarjal+Al-Aziziyah+Al-Jawf+Saudi+Arabia" target="_blank" rel="noopener noreferrer">{{ __('Open location in Maps') }}: 📍 {{ __('Al-Jawf region — Tabarjal — Al-Aziziyah neighborhood') }} ↗</a></div></article>
  <article class="footer-contact-card"><div class="footer-contact-avatar" aria-hidden="true">MS</div><div class="footer-contact-content"><h3>🇪🇬 Eng. Mohamed Saad</h3><p class="footer-contact-role">{{ __('Export Sector Manager') }}</p><a class="footer-contact-phone" href="tel:+201060113074">📞 <span dir="ltr">+20 106 011 3074</span></a><a class="footer-contact-location" href="https://www.google.com/maps/search/?api=1&query=Nubaria+Beheira+Egypt" target="_blank" rel="noopener noreferrer">{{ __('Open location in Maps') }}: 📍 {{ __('Nubaria — Beheira, Egypt') }} ↗</a></div></article>
 </div>
</section>
<div class="footer-bottom"><span class="footer-copyright-brand">© {{ date('Y') }} <img class="encap-inline-brand footer-copyright-symbol" src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="ENCAP" width="18" height="18" loading="lazy" decoding="async"> {{ __('All rights reserved.') }}</span><span>{{ __('Agricultural production · Sourcing · Processing · Export') }}</span></div></div></footer>
<script src="{{ asset('js/encap-interactions.js') }}" defer></script>
@if(request()->routeIs('home'))<script src="{{ asset('js/hero-experience.js') }}" defer></script>@endif
<script src="{{ asset('js/language-switcher.js') }}" defer></script>
<script src="{{ asset('js/site-motion.js') }}" defer></script>
</body></html>
