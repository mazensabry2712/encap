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
@php($fallbackTitle = $pageSeo['title'] ?? $defaultTitle)
@php($fallbackDescription = $pageSeo['description'] ?? $defaultDescription)
@php($pageTitle = $__env->hasSection('title') ? $__env->yieldContent('title') : $fallbackTitle)
@php($pageDescription = $__env->hasSection('meta_description') ? $__env->yieldContent('meta_description') : $fallbackDescription)
@php($robotsDirective = $__env->hasSection('robots') ? $__env->yieldContent('robots') : $seoDefaults->default_robots)
@php($canonicalHref = $__env->hasSection('canonical') ? $__env->yieldContent('canonical') : $canonicalUrl)
@php($ogImageUrl = $seoDefaults->default_og_image ? url(\Illuminate\Support\Facades\Storage::url($seoDefaults->default_og_image)) : null)
@php($organizationSchema = ['@context' => 'https://schema.org', '@graph' => [['@type' => 'Organization', 'name' => $seoDefaults->organization_name, 'url' => $seoDefaults->organization_url ?: $canonicalBase], ['@type' => 'WebSite', 'name' => $seoDefaults->organization_name, 'url' => $canonicalBase, 'inLanguage' => $locale ?? 'en']]])
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $robotsDirective }}">
    <link rel="canonical" href="{{ $canonicalHref }}">
    @if(!isset($category) || ($category->name_ar && $category->description_ar))
        <link rel="alternate" hreflang="en" href="{{ $alternateEnglishUrl }}">
        <link rel="alternate" hreflang="ar" href="{{ $alternateArabicUrl }}">
        <link rel="alternate" hreflang="x-default" href="{{ $alternateEnglishUrl }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seoDefaults->organization_name }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImageUrl)<meta property="og:image" content="{{ $ogImageUrl }}">@endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="theme-color" content="#153d2b">
    <link rel="stylesheet" href="{{ asset('css/website.css') }}">
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @stack('head')
</head>
<body>
<div class="topbar"><div class="container">{{ __('Agricultural production · Sourcing · Processing · Export') }} <span style="float:inline-end"><a href="{{ ($locale ?? 'en') === 'en' ? $alternateArabicUrl : $alternateEnglishUrl }}">{{ ($locale ?? 'en') === 'en' ? 'العربية' : 'English' }}</a></span></div></div>
<header class="container nav">
    <a class="brand" href="{{ route('home', ['locale' => $locale ?? 'en']) }}">ENCAP<span style="color:#9bbd73">.</span></a>
    <nav class="links" aria-label="{{ __('Main navigation') }}">
        <a href="{{ route('home', ['locale' => $locale ?? 'en']) }}">{{ __('Home') }}</a>
        <a href="{{ route('about', ['locale' => $locale ?? 'en']) }}">{{ __('About') }}</a>
        <a href="{{ route('products.index', ['locale' => $locale ?? 'en']) }}">{{ __('Products') }}</a>
        <a href="{{ route('contact', ['locale' => $locale ?? 'en']) }}">{{ __('Contact') }}</a>
    </nav>
    <a class="button" href="{{ route('quote.create', ['locale' => $locale ?? 'en']) }}">{{ __('Talk to our team') }} ↗</a>
</header>
<main>@yield('content')</main>
<footer class="footer"><div class="container footer-grid"><div><a class="brand" style="color:white" href="{{ route('home',['locale'=>$locale ?? 'en']) }}">ENCAP.</a><p style="max-width:400px;color:#c6d7c7">{{ __('From Egyptian agricultural production and trusted partner farms to global markets.') }}</p></div><div><b>{{ __('Explore') }}</b><div class="footer-links"><a href="{{ route('about',['locale'=>$locale ?? 'en']) }}">{{ __('About') }}</a><a href="{{ route('farms',['locale'=>$locale ?? 'en']) }}">{{ __('Farms & Production') }}</a><a href="{{ route('quality',['locale'=>$locale ?? 'en']) }}">{{ __('Quality & Processing') }}</a><a href="{{ route('export',['locale'=>$locale ?? 'en']) }}">{{ __('Export') }}</a></div></div><div><b>{{ __('Products & contact') }}</b><div class="footer-links"><a href="{{ route('products.index',['locale'=>$locale ?? 'en']) }}">{{ __('Our Products') }}</a><a href="{{ route('gallery',['locale'=>$locale ?? 'en']) }}">{{ __('Gallery') }}</a><a href="{{ route('contact',['locale'=>$locale ?? 'en']) }}">{{ __('Contact') }}</a><a href="{{ route('quote.create',['locale'=>$locale ?? 'en']) }}">{{ __('Request a Quote') }}</a></div></div></div><div class="container" style="border-top:1px solid #ffffff25;margin-top:30px;padding-top:18px;font-size:13px;color:#b9cbbb">© {{ date('Y') }} ENCAP. {{ __('All rights reserved.') }}</div></footer>
</body>
</html>
