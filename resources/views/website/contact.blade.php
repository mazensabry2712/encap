@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('title', __('Contact ENCAP | Agricultural Export Enquiries'))
@section('meta_description', __('Contact ENCAP about agricultural products, wholesale supply and export requirements.'))
@section('content')
<section class="page-head"><div class="container"><div class="eyebrow">{{ __('Get in touch') }}</div><h1>{{ __('Contact ENCAP') }}</h1><p class="muted">{{ __('Tell us what you are looking for and we will follow up with you.') }}</p></div></section>
<section class="section"><div class="container split" style="align-items:start"><div>
    <h2>{{ __('Send us a message') }}</h2>
    @if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="notice error" role="alert">{{ __('Please review the fields and try again.') }}<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ route('contact.store',['locale'=>$locale]) }}"><input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;inset-inline-start:-10000px;width:1px;height:1px" value="">@csrf
        <div class="form-grid">
            <div class="field"><label for="name">{{ __('Your name') }} *</label><input id="name" name="name" required maxlength="120" autocomplete="name" value="{{ old('name') }}"></div>
            <div class="field"><label for="email">{{ __('Email address') }} *</label><input id="email" type="email" name="email" required maxlength="190" autocomplete="email" value="{{ old('email') }}"></div>
            <div class="field"><label for="company_name">{{ __('Company name') }}</label><input id="company_name" name="company_name" maxlength="160" autocomplete="organization" value="{{ old('company_name') }}"></div>
            <div class="field"><label for="phone">{{ __('Phone (optional)') }}</label><input id="phone" name="phone" maxlength="50" autocomplete="tel" value="{{ old('phone') }}"></div>
            <div class="field full"><label for="subject">{{ __('Subject') }} *</label><input id="subject" name="subject" required maxlength="190" value="{{ old('subject') }}"></div>
            <div class="field full"><label for="message">{{ __('How can we help?') }} *</label><textarea id="message" name="message" required maxlength="5000">{{ old('message') }}</textarea></div>
            <div class="field full"><button class="button" type="submit">{{ __('Send message') }} ↗</button></div>
        </div>
    </form>
</div><aside class="contact-details"><h2>{{ __('Company details') }}</h2>
@if($settings->contact_email)<p><strong>{{ __('Email') }}</strong><br><a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></p>@endif
@if($settings->contact_phone)<p><strong>{{ __('Phone') }}</strong><br><a href="tel:{{ $settings->contact_phone }}">{{ $settings->contact_phone }}</a></p>@endif
@if($settings->whatsapp_number)<p><strong>WhatsApp</strong><br><a href="https://wa.me/{{ preg_replace('/\D+/', '', $settings->whatsapp_number) }}" rel="nofollow noopener" target="_blank">{{ $settings->whatsapp_number }}</a></p>@endif
@if(($locale === 'ar' && $settings->address_ar) || $settings->address_en)<p><strong>{{ __('Address') }}</strong><br>{{ $locale === 'ar' && $settings->address_ar ? $settings->address_ar : $settings->address_en }}</p>@endif
<p class="muted">{{ __('Company contact details are shown here once configured by an administrator.') }}</p><a class="button" href="{{ route('quote.create',['locale'=>$locale]) }}">{{ __('Request an export quote') }} ↗</a>
</aside></div></section>
@endsection
