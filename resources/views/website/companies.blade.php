@extends('website.layout')

@section('content')
<section class="page-head encap-group-companies-hero" aria-labelledby="group-companies-page-title">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ __('Our Companies') }}</span>
        </nav>
        <div class="page-intro-grid">
            <div>
                <div class="eyebrow">{{ __('Our Companies') }}</div>
                <h1 id="group-companies-page-title">{{ __('Group Companies') }}</h1>
                <p class="lead">{{ __('Explore the companies affiliated with ENCAP.') }}</p>
            </div>
            <aside class="page-aside-note">
                <strong><span aria-hidden="true">ENCAP /</span> {{ __('GROUP') }}</strong>
                {{ __('A regional company within the ENCAP group.') }}
            </aside>
        </div>
    </div>
</section>

<section class="section encap-group-companies-content" aria-labelledby="group-companies-list-title">
    <div class="container">
        <div class="encap-group-companies-panel">
            <div class="encap-group-companies-page-heading">
                <span class="encap-section-index">{{ __('ENCAP GROUP · EGYPT & SAUDI ARABIA') }}</span>
                <h2 id="group-companies-list-title">{{ __('Group Companies') }}</h2>
                <p>{{ __('Explore the companies affiliated with ENCAP.') }}</p>
            </div>

            <article class="encap-footer-group-company-card">
                <div class="encap-footer-group-company-mark" aria-hidden="true">🇸🇦</div>
                <div class="encap-footer-group-company-identity">
                    <span class="encap-footer-card-tag">{{ __('ENCAP subsidiary') }}</span>
                    <h3>{{ __('Hadaeq Al-Shamal Company') }}</h3>
                    @if($locale !== 'ar')
                        <p class="encap-footer-arabic-name" lang="ar" dir="rtl">شركة حدائق الشمال</p>
                    @endif
                </div>
                <div class="encap-footer-group-company-location">
                    <span class="encap-footer-group-company-country">{{ __('Kingdom of Saudi Arabia') }}</span>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=Aljouf+Saudi+Arabia" target="_blank" rel="noopener noreferrer">
                        <span aria-hidden="true">⌖</span> {{ __('Aljouf - Saudia') }} <span class="encap-footer-outbound" aria-hidden="true">↗</span>
                    </a>
                </div>
            </article>

            <div class="encap-group-companies-cta">
                <p>{{ __('Contact our team for business and export enquiries.') }}</p>
                <a class="encap-button encap-button-primary" href="{{ route('contact', ['locale' => $locale]) }}">{{ __('Contact our team') }} <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </div>
</section>
@endsection
