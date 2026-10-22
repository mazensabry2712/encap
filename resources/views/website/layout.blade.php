<!doctype html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
@php($seoDefaults = \App\Models\SeoSetting::current())
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
@php($ogImageUrl = $seoDefaults->default_og_image ? url(\Illuminate\Support\Facades\Storage::url($seoDefaults->default_og_image)) : null)
@php($organizationSchema = ['@context' => 'https://schema.org', '@graph' => [['@type' => 'Organization', 'name' => $seoDefaults->organization_name, 'url' => $seoDefaults->organization_url ?: $canonicalBase], ['@type' => 'WebSite', 'name' => $seoDefaults->organization_name, 'url' => $canonicalBase, 'inLanguage' => $locale ?? 'en']]])
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="color-scheme" content="light"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>{{ $pageTitle }}</title><meta name="description" content="{{ $pageDescription }}"><meta name="robots" content="{{ $robotsDirective }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if(!isset($category) || ($category->name_ar && $category->description_ar))
        <link rel="alternate" hreflang="en" href="{{ $alternateEnglishUrl }}"><link rel="alternate" hreflang="ar" href="{{ $alternateArabicUrl }}"><link rel="alternate" hreflang="x-default" href="{{ $alternateEnglishUrl }}">
    @endif
    <meta property="og:type" content="website"><meta property="og:site_name" content="{{ $seoDefaults->organization_name }}"><meta property="og:title" content="{{ $pageTitle }}"><meta property="og:description" content="{{ $pageDescription }}"><meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImageUrl)<meta property="og:image" content="{{ $ogImageUrl }}">@endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}"><meta name="theme-color" content="#08291e">
    @if(request()->routeIs('home'))
        <link rel="preconnect" href="https://images.unsplash.com">
        <link rel="preload" as="image" href="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&amp;fit=crop&amp;w=1200&amp;q=78">
    @endif
    <link rel="stylesheet" href="{{ asset('css/website.css') }}">
    <link rel="stylesheet" href="{{ asset('css/encap-redesign.css') }}">
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
<header class="site-header"><div class="container nav">
    <a class="brand" href="{{ route('home', ['locale' => $currentLocale]) }}" aria-label="ENCAP — home"><span class="brand-mark" aria-hidden="true">E</span><span class="brand-word"><strong>ENCAP</strong><small>AGRICULTURE · EXPORT</small></span></a>
    <nav class="links desktop-links" aria-label="{{ __('Main navigation') }}">
        <a href="{{ route('home', ['locale' => $currentLocale]) }}" @if(request()->routeIs('home')) aria-current="page" @endif>{{ __('Home') }}</a>
        <a href="{{ route('about', ['locale' => $currentLocale]) }}" @if(request()->routeIs('about')) aria-current="page" @endif>{{ __('About') }}</a>
        <a href="{{ route('farms', ['locale' => $currentLocale]) }}" @if(request()->routeIs('farms')) aria-current="page" @endif>{{ __('Farms & Production') }}</a>
        <a href="{{ route('products.index', ['locale' => $currentLocale]) }}" @if(request()->routeIs('products.*')) aria-current="page" @endif>{{ __('Products') }}</a>
        <a href="{{ route('quality', ['locale' => $currentLocale]) }}" @if(request()->routeIs('quality')) aria-current="page" @endif>{{ __('Quality') }}</a>
        <a href="{{ route('contact', ['locale' => $currentLocale]) }}" @if(request()->routeIs('contact')) aria-current="page" @endif>{{ __('Contact') }}</a>
    </nav>
    <div class="nav-actions">
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
        <a class="button header-cta" href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }} <span class="button-arrow">↗</span></a>
        <details class="mobile-menu"><summary aria-label="{{ __('Open navigation') }}"><span>{{ __('Menu') }}</span><span class="menu-icon" aria-hidden="true"><i></i><i></i><i></i></span></summary><nav class="mobile-nav-panel" aria-label="{{ __('Mobile navigation') }}">
            <a href="{{ route('home', ['locale' => $currentLocale]) }}">{{ __('Home') }}</a><a href="{{ route('about', ['locale' => $currentLocale]) }}">{{ __('About') }}</a><a href="{{ route('farms', ['locale' => $currentLocale]) }}">{{ __('Farms & Production') }}</a><a href="{{ route('products.index', ['locale' => $currentLocale]) }}">{{ __('Products') }}</a><a href="{{ route('quality', ['locale' => $currentLocale]) }}">{{ __('Quality') }}</a><a href="{{ route('export', ['locale' => $currentLocale]) }}">{{ __('Export') }}</a><a href="{{ route('gallery', ['locale' => $currentLocale]) }}">{{ __('Gallery') }}</a><a href="{{ route('contact', ['locale' => $currentLocale]) }}">{{ __('Contact') }}</a><a href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }} ↗</a>
        </nav></details>
    </div>
</div></header>
<main id="main-content">@yield('content')</main>
<footer class="footer"><div class="container"><div class="footer-top">
    <div><a class="brand" href="{{ route('home', ['locale' => $currentLocale]) }}"><span class="brand-mark" aria-hidden="true">E</span><span class="brand-word"><strong>ENCAP</strong><small>AGRICULTURE · EXPORT</small></span></a><p class="footer-intro">{{ __('From Egyptian agricultural production and trusted partner farms to global markets.') }}</p></div>
    <div><h2 class="footer-heading">{{ __('Explore') }}</h2><div class="footer-links"><a href="{{ route('about', ['locale' => $currentLocale]) }}">{{ __('About') }}</a><a href="{{ route('farms', ['locale' => $currentLocale]) }}">{{ __('Farms & Production') }}</a><a href="{{ route('quality', ['locale' => $currentLocale]) }}">{{ __('Quality & Processing') }}</a><a href="{{ route('export', ['locale' => $currentLocale]) }}">{{ __('Export') }}</a></div></div>
    <div><h2 class="footer-heading">{{ __('Products & contact') }}</h2><div class="footer-links"><a href="{{ route('products.index', ['locale' => $currentLocale]) }}">{{ __('Our Products') }}</a><a href="{{ route('gallery', ['locale' => $currentLocale]) }}">{{ __('Gallery') }}</a><a href="{{ route('contact', ['locale' => $currentLocale]) }}">{{ __('Contact') }}</a><a href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }}</a></div></div>
</div><div class="footer-bottom"><span>© {{ date('Y') }} ENCAP. {{ __('All rights reserved.') }}</span><span>{{ __('Agricultural production · Sourcing · Processing · Export') }}</span></div></div></footer>
<script src="{{ asset('js/encap-interactions.js') }}" defer></script>
<script src="{{ asset('js/language-switcher.js') }}" defer></script>
</body></html>
