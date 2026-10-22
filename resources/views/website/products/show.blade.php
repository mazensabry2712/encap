@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@php($name = $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en)
@php($shortDescription = $locale === 'ar' && $category->short_description_ar ? $category->short_description_ar : $category->short_description_en)
@php($description = $locale === 'ar' && $category->description_ar ? $category->description_ar : $category->description_en)
@php($varieties = $locale === 'ar' && filled($category->varieties_ar) ? $category->varieties_ar : $category->varieties_en)
@php($harvestWindow = $locale === 'ar' && filled($category->harvest_window_ar) ? $category->harvest_window_ar : $category->harvest_window_en)
@php($specifications = $locale === 'ar' && filled($category->specifications_ar) ? $category->specifications_ar : $category->specifications_en)
@php($overviewHeading = ($locale === 'ar' ? $category->overview_heading_ar : $category->overview_heading_en) ?: __('Quality supply, tailored to your needs'))
@php($enquiryHeading = ($locale === 'ar' ? $category->enquiry_note_heading_ar : $category->enquiry_note_heading_en) ?: __('Product enquiry'))
@php($enquiryText = ($locale === 'ar' ? $category->enquiry_note_text_ar : $category->enquiry_note_text_en) ?: __('Share your target quantity and destination to help us review current availability.'))
@php($bottomCtaHeading = ($locale === 'ar' ? $category->bottom_cta_heading_ar : $category->bottom_cta_heading_en) ?: __('Let’s confirm the details that matter.'))
@php($bottomCtaText = ($locale === 'ar' ? $category->bottom_cta_text_ar : $category->bottom_cta_text_en) ?: __('Tell us the product form, quantity, packaging and destination you need.'))
@php($siteBase = rtrim(config('app.url'), '/'))
@php($breadcrumbSchema = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => $siteBase . '/' . $locale], ['@type' => 'ListItem', 'position' => 2, 'name' => __('Products'), 'item' => $siteBase . '/' . $locale . '/products'], ['@type' => 'ListItem', 'position' => 3, 'name' => $name, 'item' => $siteBase . '/' . $locale . '/products/' . $category->slug]]])
@push('head')<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>@endpush
@section('content')
<section class="page-head">
    <div class="container">
        <div class="breadcrumbs"><a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a> &nbsp; / &nbsp; <a href="{{ route('products.index', ['locale' => $locale]) }}">{{ __('Products') }}</a> &nbsp; / &nbsp; {{ $name }}</div>
        <div class="page-intro-grid">
            <div>
                <div class="eyebrow"><img class="encap-inline-brand" src="{{ asset('assets/favicone.png') }}" alt="ENCAP" width="22" height="22" decoding="async"> {{ __('Product category') }}</div>
                <h1>{{ $name }}</h1>
                @if(filled($shortDescription))<p class="lead">{{ $shortDescription }}</p>@endif
            </div>
            @if($category->show_enquiry_note)
                <aside class="page-aside-note"><strong>{{ $enquiryHeading }}</strong>{{ $enquiryText }}</aside>
            @endif
        </div>
    </div>
</section>

<section>
    <div class="container product-detail{{ $category->show_product_images ? '' : ' product-detail-no-images' }}">
        @if($category->show_product_images)
            <div class="product-detail-media">
                <div class="product-gallery-main">
                    @if($category->image)
                        <img data-gallery-main src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $name }}" width="900" height="760" fetchpriority="high" decoding="async">
                    @else
                        <div class="product-visual" role="img" aria-label="{{ $name }}"></div>
                    @endif
                </div>
                @if($category->gallery)
                    <div class="product-gallery-thumbs" aria-label="{{ $locale === 'ar' ? 'صور المنتج' : 'Product gallery' }}">
                        @foreach($category->gallery as $image)
                            <button class="product-gallery-thumb" type="button" data-gallery-thumb data-image-src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" data-image-alt="{{ $name }}" aria-label="{{ $locale === 'ar' ? 'عرض صورة' : 'View product image' }} {{ $loop->iteration }}" aria-pressed="false">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" alt="" loading="lazy" width="300" height="220">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="product-detail-copy">
            @if($category->show_description)
                <div class="product-overview">
                    <div class="eyebrow">{{ __('Product overview') }}</div>
                    <h2>{{ $overviewHeading }}</h2>
                    <p>{{ $description ?: ($locale === 'ar' ? 'تواصل معنا لمعرفة التوافر الحالي والمواصفات والتعبئة المناسبة لاحتياجاتك.' : 'Contact our team to confirm current availability, specifications and packaging for your requirements.') }}</p>
                </div>
            @endif

            @if($category->show_available_forms)
                <div class="detail-block">
                    <h3 class="detail-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-leaf"></use></svg>{{ __('Available formats') }}</h3>
                    @if($category->available_forms)
                        <div class="format-pills">@foreach($category->available_forms as $form)<span class="format-pill">{{ __($form) }}</span>@endforeach</div>
                    @else
                        <p>{{ __('Available formats are confirmed for each enquiry.') }}</p>
                    @endif
                </div>
            @endif

            @if($category->show_packaging && ($category->packaging_en || $category->packaging_ar))
                <div class="detail-block">
                    <h3 class="detail-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-package"></use></svg>{{ __('Packaging') }}</h3>
                    <p>{{ $locale === 'ar' && $category->packaging_ar ? $category->packaging_ar : $category->packaging_en }}</p>
                </div>
            @endif

            @if($category->show_specifications && (filled($varieties) || filled($harvestWindow) || filled($specifications)))
                <div class="detail-block product-specifications">
                    <h3 class="detail-heading">{{ __('Product specifications') }}</h3>
                    <dl class="product-spec-grid">
                        @if(filled($varieties))<div><dt>{{ __('Varieties') }}</dt><dd>{{ $varieties }}</dd></div>@endif
                        @if(filled($harvestWindow))<div><dt>{{ __('Harvest window') }}</dt><dd>{{ $harvestWindow }}</dd></div>@endif
                        @if(filled($specifications))<div><dt>{{ __('Specifications') }}</dt><dd>{{ $specifications }}</dd></div>@endif
                    </dl>
                </div>
            @endif

            <a class="button" href="{{ route('quote.create', ['locale' => $locale, 'category' => $category->id]) }}">{{ __('Request a Quote for this product') }} <span class="button-arrow">↗</span></a>
        </div>
    </div>
</section>

@if($category->show_bottom_cta)
    <section class="section soft">
        <div class="container">
            <div class="cta">
                <div>
                    <div class="eyebrow">{{ __('For importers & distributors') }}</div>
                    <h2>{{ $bottomCtaHeading }}</h2>
                    <p>{{ $bottomCtaText }}</p>
                </div>
                <a class="button" href="{{ route('quote.create', ['locale' => $locale, 'category' => $category->id]) }}">{{ __('Request a Quote') }} <span class="button-arrow">↗</span></a>
            </div>
        </div>
    </section>
@endif
@endsection
