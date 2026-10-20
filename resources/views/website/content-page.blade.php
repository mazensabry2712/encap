@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('title', $pageSeo['title'])
@section('meta_description', $pageSeo['description'])
@section('content')
<section class="page-head"><div class="container"><div class="breadcrumbs"><a href="{{ route('home',['locale'=>$locale]) }}">{{ __('Home') }}</a> / {{ $pageContent['eyebrow'] }}</div><div class="eyebrow">{{ $pageContent['eyebrow'] }}</div><h1>{{ $pageContent['title'] }}</h1><p class="muted" style="max-width:760px;font-size:18px">{{ $pageContent['intro'] }}</p></div></section>
<section class="section"><div class="container">
@if($page === 'gallery')
    @if($categories->isNotEmpty())<div class="gallery-grid">@foreach($categories as $category)<figure style="margin:0"><img src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}" loading="lazy" width="500" height="360"><figcaption style="padding:10px 0;font-weight:700">{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}</figcaption></figure>@endforeach</div>@else<p class="muted">{{ __('Approved farm and product photography will be added to the gallery as it becomes available.') }}</p>@endif
@endif
<div class="content-copy"><p>{{ $pageContent['body'] }}</p></div>
<div class="cta" style="margin-top:32px"><div><h2>{{ __('Looking for a reliable supply partner?') }}</h2><p class="muted">{{ __('Share your product, quantity and destination requirements with our team.') }}</p></div><a class="button" href="{{ route('quote.create',['locale'=>$locale]) }}">{{ __('Request a Quote') }} ↗</a></div>
</div></section>
@endsection
