@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@php($categoryOffset = $categories->firstItem() ?? 1)
@php($heroCategories = $categories->getCollection()->take(3)->values())
@section('content')
<section class="encap-catalog-hero" aria-labelledby="encap-catalog-title">
    <div class="container">
        <nav class="encap-catalog-breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ __('Products') }}</span>
        </nav>

        <div class="encap-catalog-hero-grid" data-encap-hero>
            <div class="encap-catalog-hero-copy">
                <div class="encap-catalog-eyebrow">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-leaf"></use></svg>
                    <span>{{ __('Our catalogue') }}</span>
                    <i aria-hidden="true"></i>
                    <span>{{ __('Agricultural production') }}</span>
                </div>

                <h1 id="encap-catalog-title">{{ __('Products for global markets') }}</h1>
                <p class="encap-catalog-hero-tagline">{{ __('From Egyptian fields to global markets.') }}</p>
                <p class="encap-catalog-hero-description">{{ __('Browse the current categories, then share the form, quantity and destination you need.') }}</p>

                <div class="encap-catalog-hero-actions">
                    <a class="encap-catalog-button encap-catalog-button--light" href="#encap-catalog-grid">
                        {{ __('Explore the collection') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                    </a>
                    <a class="encap-catalog-button encap-catalog-button--outline" href="{{ route('quote.create', ['locale' => $locale]) }}">
                        {{ __('Request a Quote') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                    </a>
                </div>

                <p class="encap-catalog-hero-footnote">
                    <span aria-hidden="true"></span>
                    {{ __('Product details are confirmed for each enquiry.') }}
                </p>
            </div>

            <div class="encap-catalog-hero-visual" aria-hidden="true">
                <div class="encap-catalog-orbit encap-catalog-orbit--outer"></div>
                <div class="encap-catalog-orbit encap-catalog-orbit--middle"></div>
                <div class="encap-catalog-orbit encap-catalog-orbit--inner"></div>

                @if($heroCategories->isNotEmpty())
                    @php($heroImageClasses = ['primary', 'secondary', 'tertiary'])
                    @foreach($heroCategories as $heroIndex => $heroCategory)
                        <figure class="encap-catalog-hero-image encap-catalog-hero-image--{{ $heroImageClasses[$heroIndex] }}">
                            @if($heroCategory->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($heroCategory->image) }}" alt="" width="640" height="760" @if($heroIndex === 0) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                            @else
                                <span class="encap-catalog-hero-image-placeholder"><svg viewBox="0 0 24 24"><use href="#icon-sprout"></use></svg></span>
                            @endif
                            <figcaption>{{ $locale === 'ar' && $heroCategory->name_ar ? $heroCategory->name_ar : $heroCategory->name_en }}</figcaption>
                        </figure>
                    @endforeach
                @else
                    <div class="encap-catalog-hero-emblem">
                        <span class="encap-catalog-emblem-halo"></span>
                        <svg viewBox="0 0 24 24"><use href="#icon-sprout"></use></svg>
                        <span class="encap-catalog-emblem-word">ENCAP</span>
                    </div>
                @endif

                <div class="encap-catalog-scene-note">
                    <span class="encap-catalog-scene-note-dot"></span>
                    <span>{{ __('Agricultural production') }}</span>
                    <span class="encap-catalog-scene-note-arrow">↗</span>
                    <span>{{ __('Processing & Export') }}</span>
                </div>
                <span class="encap-catalog-scene-index">E / 01</span>
            </div>
        </div>
    </div>
</section>

<section class="encap-catalog-summary-band" aria-label="{{ __('Our catalogue') }}">
    <div class="container encap-catalog-summary-grid">
        <div class="encap-catalog-summary-count">
            <strong>{{ number_format($categories->total()) }}</strong>
            <span>{{ __('Published categories') }}</span>
        </div>
        <div class="encap-catalog-summary-item">
            <span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-sprout"></use></svg></span>
            <span>{{ __('Agricultural production') }}</span>
        </div>
        <div class="encap-catalog-summary-item">
            <span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-network"></use></svg></span>
            <span>{{ __('Sourcing') }}</span>
        </div>
        <div class="encap-catalog-summary-item">
            <span class="encap-catalog-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-globe"></use></svg></span>
            <span>{{ __('Processing & Export') }}</span>
        </div>
    </div>
</section>

<section class="encap-catalog-section" id="encap-catalog-grid" aria-labelledby="encap-catalog-section-title">
    <div class="container">
        <div class="encap-catalog-section-heading">
            <div class="encap-catalog-heading">
                <span class="encap-catalog-kicker">{{ __('Explore the range') }} <i aria-hidden="true"></i></span>
                <h2 id="encap-catalog-section-title">{{ __('Find the right product category.') }}</h2>
                <p>{{ __('Explore our product categories, then share your required form, quantity and destination so availability can be confirmed for your enquiry.') }}</p>
            </div>
            <div class="encap-catalog-heading-aside">
                <span class="encap-catalog-heading-number">01 <i>/</i> {{ str_pad((string) max(1, (int) ceil($categories->total() / 12)), 2, '0', STR_PAD_LEFT) }}</span>
                <p>{{ __('Product formats vary by category and are confirmed for each enquiry.') }}</p>
            </div>
        </div>

        @if($categories->isEmpty())
            <div class="encap-catalog-empty">
                <div class="encap-catalog-empty-art" aria-hidden="true">
                    <span></span>
                    <svg viewBox="0 0 24 24"><use href="#icon-sprout"></use></svg>
                </div>
                <div class="encap-catalog-empty-copy">
                    <span class="encap-catalog-kicker">{{ __('Our catalogue') }}</span>
                    <h3>{{ __('The catalogue is being prepared.') }}</h3>
                    <p>{{ __('Product categories will be published here soon. You can still contact us about your requirements.') }}</p>
                    <a class="encap-catalog-text-link" href="{{ route('quote.create', ['locale' => $locale]) }}">
                        {{ __('Contact our team') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                    </a>
                </div>
            </div>
        @else
            <div class="encap-catalog-grid">
                @foreach($categories as $category)
                    @php($categoryName = $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en)
                    @php($categoryDescription = $locale === 'ar' && $category->short_description_ar ? $category->short_description_ar : $category->short_description_en)
                    @php($categoryForms = $category->available_forms ?? [])
                    <a class="encap-catalog-card" data-tilt href="{{ route('products.show', ['locale' => $locale, 'category' => $category->slug]) }}" aria-label="{{ __('View details') }}: {{ $categoryName }}">
                        <div class="encap-catalog-card-media">
                            <span class="encap-catalog-card-index">{{ str_pad($categoryOffset + $loop->index, 2, '0', STR_PAD_LEFT) }}</span>
                            @if($category->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $categoryName }}" width="720" height="520" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                            @else
                                <span class="encap-catalog-card-placeholder" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><use href="#icon-sprout"></use></svg>
                                    <span>ENCAP</span>
                                </span>
                            @endif
                            <span class="encap-catalog-card-open" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><use href="#icon-arrow-up-right"></use></svg>
                            </span>
                        </div>
                        <div class="encap-catalog-card-body">
                            <div class="encap-catalog-card-title-row">
                                <div>
                                    <span class="encap-catalog-card-eyebrow">{{ __('Product category') }}</span>
                                    <h3>{{ $categoryName }}</h3>
                                </div>
                                <span class="encap-catalog-card-mark" aria-hidden="true">↗</span>
                            </div>
                            @if(filled($categoryDescription))
                                <p class="encap-catalog-card-description">{{ $categoryDescription }}</p>
                            @else
                                <p class="encap-catalog-card-description">{{ __('Product details are confirmed for each enquiry.') }}</p>
                            @endif
                            <div class="encap-catalog-card-footer">
                                @if(count($categoryForms))
                                    <div class="encap-catalog-formats" aria-label="{{ __('Available formats') }}">
                                        @foreach(array_slice($categoryForms, 0, 3) as $form)
                                            <span>{{ __($form) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="encap-catalog-formats-note">{{ __('Available formats are confirmed for each enquiry.') }}</span>
                                @endif
                                <span class="encap-catalog-card-link">{{ __('View details') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($categories->hasPages())
                <div class="encap-catalog-pagination" aria-label="{{ __('Pagination') }}">
                    {{ $categories->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

<section class="encap-catalog-cta-section">
    <div class="container">
        <div class="encap-catalog-cta">
            <div class="encap-catalog-cta-orbit" aria-hidden="true">
                <span></span><span></span>
                <div><svg viewBox="0 0 24 24"><use href="#icon-globe"></use></svg></div>
            </div>
            <div class="encap-catalog-cta-copy">
                <span class="encap-catalog-kicker">{{ __('Have a specific requirement?') }}</span>
                <h2>{{ __('Let’s discuss your product and destination.') }}</h2>
                <p>{{ __('Share your preferred form, quantity and packaging needs in one enquiry.') }}</p>
                <a class="encap-catalog-button encap-catalog-button--light" href="{{ route('quote.create', ['locale' => $locale]) }}">
                    {{ __('Request a Quote') }}
                    <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>
                </a>
            </div>
            <span class="encap-catalog-cta-index" aria-hidden="true">ENCAP / 01</span>
        </div>
    </div>
</section>
@endsection
