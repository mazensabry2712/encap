@extends('website.layout')
@php($locale = request()->route('locale', 'en'))
@section('content')
<section class="page-head"><div class="container"><div class="eyebrow">{{ __('Business enquiries') }}</div><h1>{{ __('Request an export quote') }}</h1><p class="muted">{{ __('Share your requirements and our team can follow up with availability and next steps.') }}</p></div></section>
<section class="section"><div class="container" style="max-width:820px">@if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="notice error" role="alert">{{ __('Please review the highlighted fields and try again.') }}<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ route('quote.store',['locale'=>$locale]) }}">@csrf<div class="form-grid">
<div class="field"><label for="contact_name">{{ __('Contact name') }} *</label><input id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required maxlength="120" autocomplete="name"></div>
<div class="field"><label for="company_name">{{ __('Company name') }}</label><input id="company_name" name="company_name" value="{{ old('company_name') }}" maxlength="160" autocomplete="organization"></div>
<div class="field"><label for="email">{{ __('Business email') }} *</label><input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email"></div>
<div class="field"><label for="country">{{ __('Country') }} *</label><input id="country" name="country" value="{{ old('country') }}" required maxlength="100" autocomplete="country-name"></div>
<div class="field"><label for="category_id">{{ __('Product category') }}</label><select id="category_id" name="category_id"><option value="">{{ __('Choose a category') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $selectedCategory) == $category->id)>{{ $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}</option>@endforeach</select></div>
<div class="field"><label for="product_form">{{ __('Product form') }}</label><select id="product_form" name="product_form"><option value="">{{ __('No preference') }}</option><option value="fresh" @selected(old('product_form')==='fresh')>{{ __('Fresh') }}</option><option value="frozen" @selected(old('product_form')==='frozen')>{{ __('Frozen') }}</option><option value="processed" @selected(old('product_form')==='processed')>{{ __('Processed') }}</option></select></div>
<div class="field"><label for="quantity">{{ __('Estimated quantity') }}</label><input id="quantity" type="number" min="0.001" step="0.001" name="quantity" value="{{ old('quantity') }}"></div>
<div class="field"><label for="quantity_unit">{{ __('Unit') }}</label><input id="quantity_unit" name="quantity_unit" maxlength="40" placeholder="e.g. metric tons" value="{{ old('quantity_unit') }}"></div>
<div class="field full"><label for="packaging_requirements">{{ __('Packaging requirements') }}</label><input id="packaging_requirements" name="packaging_requirements" maxlength="255" value="{{ old('packaging_requirements') }}"></div>
<div class="field full"><label for="message">{{ __('Tell us about your requirements') }} *</label><textarea id="message" name="message" required maxlength="5000">{{ old('message') }}</textarea></div>
<div class="field full"><button class="button" type="submit">{{ __('Send quote request') }} ↗</button><p class="muted" style="font-size:13px">{{ __('Your details are used to respond to this business enquiry.') }}</p></div>
</div></form></div></section>
@endsection
