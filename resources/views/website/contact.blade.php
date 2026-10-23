@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@php($contactAddress = $locale === 'ar' ? ($settings->address_ar ?: $settings->address_en) : ($settings->address_en ?: $settings->address_ar))
@php($businessHours = $locale === 'ar' ? ($settings->business_hours_ar ?: $settings->business_hours_en) : ($settings->business_hours_en ?: $settings->business_hours_ar))
@php($hasDirectContact = filled($settings->contact_email) || filled($settings->contact_phone) || filled($settings->whatsapp_number) || filled($contactAddress) || filled($businessHours))
@section('content')
<section class="encap-contact-hero" aria-labelledby="encap-contact-title">
    <div class="encap-contact-hero-orbit encap-contact-hero-orbit--one" aria-hidden="true"></div>
    <div class="encap-contact-hero-orbit encap-contact-hero-orbit--two" aria-hidden="true"></div>
    <div class="container encap-contact-hero-container">
        <nav class="breadcrumbs encap-contact-breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ __('Contact') }}</span>
        </nav>
        <div class="encap-contact-hero-grid">
            <div class="encap-contact-hero-copy">
                <span class="encap-contact-kicker"><span aria-hidden="true"></span>{{ __('Get in touch') }}</span>
                <h1 id="encap-contact-title">{{ __('Let’s make the next step clear.') }}</h1>
                <p>{{ __('Whether you are exploring products or planning a supply request, tell us what you need and where it needs to go.') }}</p>
                <div class="encap-contact-hero-actions">
                    <a class="encap-button encap-button-light" href="#contact-form">{{ __('Send us a message') }} <span aria-hidden="true">↘</span></a>
                    <a class="encap-button encap-button-outline" href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request an export quote') }} <span aria-hidden="true">↗</span></a>
                </div>
                <div class="encap-contact-hero-meta">
                    <span>{{ __('General enquiries') }}</span><i aria-hidden="true"></i>
                    <span>{{ __('Product questions') }}</span><i aria-hidden="true"></i>
                    <span>{{ __('Export coordination') }}</span>
                </div>
            </div>
            <div class="encap-contact-hero-visual" aria-hidden="true">
                <div class="encap-contact-orbit encap-contact-orbit--outer"></div>
                <div class="encap-contact-orbit encap-contact-orbit--middle"></div>
                <div class="encap-contact-orbit encap-contact-orbit--inner"></div>
                <div class="encap-contact-orbit-axis"></div>
                <div class="encap-contact-orbit-core">
                    <img src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" width="56" height="56" decoding="async">
                    <span>ENCAP</span>
                    <small>{{ __('Contact ENCAP directly') }}</small>
                </div>
                <div class="encap-contact-orbit-node encap-contact-orbit-node--product"><svg viewBox="0 0 24 24"><use href="#icon-sprout"></use></svg><span>{{ __('Product') }}</span></div>
                <div class="encap-contact-orbit-node encap-contact-orbit-node--quantity"><svg viewBox="0 0 24 24"><use href="#icon-package"></use></svg><span>{{ __('Quantity') }}</span></div>
                <div class="encap-contact-orbit-node encap-contact-orbit-node--destination"><svg viewBox="0 0 24 24"><use href="#icon-globe"></use></svg><span>{{ __('Destination') }}</span></div>
            </div>
        </div>
        <div class="encap-contact-hero-baseline">
            <span>{{ __('Clear questions. Clear next steps.') }}</span>
            <span aria-hidden="true">✳</span>
            <span>{{ __('From origin to market') }}</span>
        </div>
    </div>
</section>

<section class="encap-contact-workspace" id="contact-form" aria-labelledby="encap-contact-form-title">
    <div class="container">
        <div class="encap-contact-section-heading">
            <div>
                <span class="encap-section-index">{{ __('Start a conversation') }}</span>
                <h2 id="encap-contact-form-title">{{ __('Tell us a little about your enquiry.') }}</h2>
            </div>
            <p>{{ __('Use the general contact form for questions. If you already know the product, quantity, packaging and destination, the quote form is designed for those details.') }}</p>
        </div>

        <div class="encap-contact-grid">
            <div class="encap-contact-form-panel">
                <div class="encap-contact-form-heading">
                    <span class="encap-contact-form-index" aria-hidden="true">01</span>
                    <div>
                        <h3>{{ __('Send us a message') }}</h3>
                        <p>{{ __('Share a little context and our team can direct your enquiry to the right place.') }}</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="encap-contact-feedback encap-contact-feedback--success" role="status" tabindex="-1">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-shield-check"></use></svg>
                        <div><strong>{{ __('Message received') }}</strong><p>{{ session('success') }}</p></div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="encap-contact-feedback encap-contact-feedback--error" role="alert" tabindex="-1">
                        <strong>{{ __('Please review the fields and try again.') }}</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form class="encap-contact-form" method="post" action="{{ route('contact.store', ['locale' => $locale]) }}">
                    @csrf
                    <div class="encap-contact-honeypot" aria-hidden="true">
                        <label for="website">{{ __('Website') }}</label>
                        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off" value="">
                    </div>
                    <div class="encap-contact-form-topline">
                        <p>{{ __('Fields marked with an asterisk are required. Add as much detail as you can.') }}</p>
                        <span><i aria-hidden="true"></i>{{ __('Required') }}</span>
                    </div>

                    <div class="form-grid">
                        <div class="field @error('name') has-error @enderror">
                            <label for="name">{{ __('Your name') }} <span aria-hidden="true">*</span></label>
                            <input id="name" class="@error('name') is-invalid @enderror" type="text" name="name" required maxlength="120" autocomplete="name" value="{{ old('name') }}" @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
                            @error('name')<p class="encap-contact-field-error" id="name-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('email') has-error @enderror">
                            <label for="email">{{ __('Email address') }} <span aria-hidden="true">*</span></label>
                            <input id="email" class="@error('email') is-invalid @enderror" type="email" name="email" required maxlength="190" autocomplete="email" value="{{ old('email') }}" @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                            @error('email')<p class="encap-contact-field-error" id="email-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('company_name') has-error @enderror">
                            <label for="company_name">{{ __('Company name') }}</label>
                            <input id="company_name" class="@error('company_name') is-invalid @enderror" type="text" name="company_name" maxlength="160" autocomplete="organization" value="{{ old('company_name') }}" @if($errors->has('company_name')) aria-invalid="true" aria-describedby="company-name-error" @endif>
                            @error('company_name')<p class="encap-contact-field-error" id="company-name-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field @error('phone') has-error @enderror">
                            <label for="phone">{{ __('Phone (optional)') }}</label>
                            <input id="phone" class="@error('phone') is-invalid @enderror" type="tel" name="phone" maxlength="50" autocomplete="tel" value="{{ old('phone') }}" @if($errors->has('phone')) aria-invalid="true" aria-describedby="phone-error" @endif>
                            @error('phone')<p class="encap-contact-field-error" id="phone-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full @error('subject') has-error @enderror">
                            <label for="subject">{{ __('Subject') }} <span aria-hidden="true">*</span></label>
                            <input id="subject" class="@error('subject') is-invalid @enderror" type="text" name="subject" required maxlength="190" value="{{ old('subject') }}" @if($errors->has('subject')) aria-invalid="true" aria-describedby="subject-error" @endif>
                            @error('subject')<p class="encap-contact-field-error" id="subject-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full @error('message') has-error @enderror">
                            <label for="message">{{ __('How can we help?') }} <span aria-hidden="true">*</span></label>
                            <textarea id="message" class="@error('message') is-invalid @enderror" name="message" required maxlength="5000" rows="6" @if($errors->has('message')) aria-invalid="true" aria-describedby="message-hint message-error" @else aria-describedby="message-hint" @endif>{{ old('message') }}</textarea>
                            <p class="field-help" id="message-hint">{{ __('Include product details, timing or destination information if you already have them.') }}</p>
                            @error('message')<p class="encap-contact-field-error" id="message-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field full encap-contact-submit-row">
                            <button class="button encap-contact-submit" type="submit">{{ __('Send message') }} <span class="button-arrow" aria-hidden="true">↗</span></button>
                            <p><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-shield-check"></use></svg>{{ __('Your details are used to respond to this business enquiry.') }}</p>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="encap-contact-details">
                <div class="encap-contact-details-heading">
                    <span class="encap-section-index">{{ __('Contact details') }}</span>
                    <h2>{{ __('Let’s start a conversation.') }}</h2>
                    <p>{{ __('Reach out through the official channels configured for ENCAP.') }}</p>
                </div>

                @if($hasDirectContact)
                    <div class="encap-contact-channels">
                        @if(filled($settings->contact_email))
                            <article class="encap-contact-channel">
                                <span class="encap-contact-channel-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3.5 6.5h17v11h-17z"/><path d="m4 7 8 6 8-6"/></svg></span>
                                <div><span>{{ __('Email') }}</span><a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></div>
                                <span class="encap-contact-channel-arrow" aria-hidden="true">↗</span>
                            </article>
                        @endif
                        @if(filled($settings->contact_phone))
                            <article class="encap-contact-channel">
                                <span class="encap-contact-channel-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3.5h3l1.4 4-2 1.6a14 14 0 0 0 5.5 5.5l1.6-2 4 1.4v3c0 1.1-.9 2-2 2A15.5 15.5 0 0 1 5 5.5c0-1.1.9-2 2-2Z"/></svg></span>
                                <div><span>{{ __('Phone') }}</span><a href="tel:{{ $settings->contact_phone }}">{{ $settings->contact_phone }}</a></div>
                                <span class="encap-contact-channel-arrow" aria-hidden="true">↗</span>
                            </article>
                        @endif
                        @if(filled($settings->whatsapp_number))
                            <article class="encap-contact-channel">
                                <span class="encap-contact-channel-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.5a8.2 8.2 0 0 1-12.1 7.2L3.5 20l1.3-4.1A8.2 8.2 0 1 1 20 11.5Z"/><path d="M8.5 8.2c.6 2.7 2.2 4.3 4.9 5"/></svg></span>
                                <div><span>WhatsApp</span><a href="https://wa.me/{{ preg_replace('/\D+/', '', $settings->whatsapp_number) }}" rel="nofollow noopener noreferrer" target="_blank">{{ $settings->whatsapp_number }}</a></div>
                                <span class="encap-contact-channel-arrow" aria-hidden="true">↗</span>
                            </article>
                        @endif
                        @if(filled($contactAddress))
                            <article class="encap-contact-channel encap-contact-channel--address">
                                <span class="encap-contact-channel-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg></span>
                                <div><span>{{ __('Address') }}</span><p>{{ $contactAddress }}</p><a class="encap-contact-map-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($contactAddress) }}" target="_blank" rel="noopener noreferrer">{{ __('Open in Maps') }} ↗</a></div>
                            </article>
                        @endif
                        @if(filled($businessHours))
                            <article class="encap-contact-channel encap-contact-channel--hours">
                                <span class="encap-contact-channel-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></svg></span>
                                <div><span>{{ __('Business hours') }}</span><p>{{ $businessHours }}</p></div>
                            </article>
                        @endif
                    </div>
                @else
                    <div class="encap-contact-not-configured">
                        <span aria-hidden="true">01</span>
                        <p>{{ __('Company contact details are shown here once configured by an administrator.') }}</p>
                    </div>
                @endif

                <div class="encap-contact-quote-note">
                    <span class="encap-contact-quote-note-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-package"></use></svg></span>
                    <div><h3>{{ __('Need an export quote?') }}</h3><p>{{ __('Product, volume, form, packaging and destination help us understand your requirement.') }}</p><a href="{{ route('quote.create', ['locale' => $locale]) }}">{{ __('Request an export quote') }} <span aria-hidden="true">↗</span></a></div>
                </div>
            </aside>
        </div>

        <div class="encap-contact-next" aria-labelledby="encap-contact-next-title">
            <div class="encap-contact-next-heading">
                <span class="encap-section-index">{{ __('What happens next') }}</span>
                <h2 id="encap-contact-next-title">{{ __('A clear start to the conversation.') }}</h2>
            </div>
            <div class="encap-contact-next-grid">
                <article class="encap-contact-next-card"><span>01</span><div><h3>{{ __('Review your message') }}</h3><p>{{ __('We review the question and the information you have shared.') }}</p></div></article>
                <article class="encap-contact-next-card"><span>02</span><div><h3>{{ __('Clarify the requirement') }}</h3><p>{{ __('Product details, availability or destination requirements may need confirmation.') }}</p></div></article>
                <article class="encap-contact-next-card"><span>03</span><div><h3>{{ __('Agree on next steps') }}</h3><p>{{ __('Our team can discuss the appropriate next step for your enquiry.') }}</p></div></article>
            </div>
        </div>
    </div>
</section>
@endsection
