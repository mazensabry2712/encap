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
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
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
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @stack('head')
</head>
<body>
@php($currentLocale = $locale ?? 'en')
<div class="topbar"><div class="container topbar-inner"><span class="topbar-note">{{ __('Agricultural production · Sourcing · Processing · Export') }}</span><a href="{{ $currentLocale === 'en' ? $alternateArabicUrl : $alternateEnglishUrl }}" lang="{{ $currentLocale === 'en' ? 'ar' : 'en' }}">{{ $currentLocale === 'en' ? 'العربية' : 'English' }} ↗</a></div></div>
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
    <div class="nav-actions"><a class="button header-cta" href="{{ route('quote.create', ['locale' => $currentLocale]) }}">{{ __('Request a Quote') }} <span class="button-arrow">↗</span></a>
        <details class="mobile-menu"><summary aria-label="{{ __('Open navigation') }}"><span>Menu</span><span class="menu-icon" aria-hidden="true"><i></i><i></i><i></i></span></summary><nav class="mobile-nav-panel" aria-label="{{ __('Mobile navigation') }}">
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
</body></html>
