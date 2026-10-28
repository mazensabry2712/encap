@extends('admin.layout')
@section('page-title', $company->exists ? __('Edit company') : __('Create company'))
@section('content')
<div class="admin-page-intro"><div><p>{{ __('Maintain company directory details and publish only information confirmed by the ENCAP team.') }}</p></div><div class="admin-actions"><a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('← All companies') }}</a></div></div>

<form class="admin-panel admin-form-panel" method="post" enctype="multipart/form-data" action="{{ $company->exists ? route('admin.companies.update', $company) : route('admin.companies.store') }}">
@csrf @if($company->exists)@method('PUT')@endif
<div class="admin-form-grid">
    <h2 class="admin-section-title">{{ __('Basic information') }}</h2>
    <div class="admin-field"><label for="name_en">{{ __('Name (English) *') }}</label><input id="name_en" name="name_en" required maxlength="160" value="{{ old('name_en', $company->name_en) }}"></div>
    <div class="admin-field"><label for="name_ar">{{ __('Name (Arabic)') }}</label><input id="name_ar" name="name_ar" maxlength="160" dir="auto" value="{{ old('name_ar', $company->name_ar) }}"></div>
    <div class="admin-field"><label for="slug">{{ __('URL slug *') }}</label><input id="slug" name="slug" required pattern="[A-Za-z0-9_-]+" value="{{ old('slug', $company->slug) }}"></div>
    <div class="admin-field"><label for="sort_order">{{ __('Display order *') }}</label><input id="sort_order" type="number" min="0" name="sort_order" required value="{{ old('sort_order', $company->sort_order ?? 0) }}"></div>
    <div class="admin-field full"><label for="short_description_en">{{ __('Short description (English)') }}</label><input id="short_description_en" name="short_description_en" maxlength="500" value="{{ old('short_description_en', $company->short_description_en) }}"></div>
    <div class="admin-field full"><label for="short_description_ar">{{ __('Short description (Arabic)') }}</label><input id="short_description_ar" name="short_description_ar" maxlength="500" dir="auto" value="{{ old('short_description_ar', $company->short_description_ar) }}"></div>
    <div class="admin-field"><label for="description_en">{{ __('Full description (English)') }}</label><textarea id="description_en" name="description_en" maxlength="20000">{{ old('description_en', $company->description_en) }}</textarea></div>
    <div class="admin-field"><label for="description_ar">{{ __('Full description (Arabic)') }}</label><textarea id="description_ar" name="description_ar" maxlength="20000" dir="auto">{{ old('description_ar', $company->description_ar) }}</textarea></div>
    <h2 class="admin-section-title">{{ __('Company location and links') }}</h2>
    <div class="admin-field"><label for="country_en">{{ __('Country (English)') }}</label><input id="country_en" name="country_en" maxlength="120" value="{{ old('country_en', $company->country_en) }}"></div>
    <div class="admin-field"><label for="country_ar">{{ __('Country (Arabic)') }}</label><input id="country_ar" name="country_ar" maxlength="120" dir="auto" value="{{ old('country_ar', $company->country_ar) }}"></div>
    <div class="admin-field"><label for="location_en">{{ __('Location (English)') }}</label><input id="location_en" name="location_en" maxlength="160" value="{{ old('location_en', $company->location_en) }}"></div>
    <div class="admin-field"><label for="location_ar">{{ __('Location (Arabic)') }}</label><input id="location_ar" name="location_ar" maxlength="160" dir="auto" value="{{ old('location_ar', $company->location_ar) }}"></div>
    <div class="admin-field full"><label for="website_url">{{ __('Company website URL') }}</label><input id="website_url" type="url" name="website_url" maxlength="500" value="{{ old('website_url', $company->website_url) }}"></div>
    <h2 class="admin-section-title">{{ __('Company photography') }}</h2>
    <div class="admin-field full">
        @if($company->image)<div class="admin-company-image-current"><img src="{{ \App\Support\EncapImage::url($company->image) }}" alt="{{ $company->name_en }}" width="180" height="140" loading="lazy"><span>{{ __('Current main image') }}</span></div>@endif
        <label for="image">{{ __('Upload main image') }}</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/avif"><small>{{ __('JPG, PNG, WebP or AVIF · maximum 5 MB. A new upload replaces the current image.') }}</small>
    </div>
    <div class="admin-field full">
        <label for="library_image">{{ __('Or choose an existing image from the central library') }}</label>
        <select id="library_image" name="library_image"><option value="">{{ __('Keep current image / use uploaded image') }}</option>@foreach($mediaAssets as $path => $asset)<option value="{{ $path }}" @selected(old('library_image') === $path)>{{ $asset['label'] }} — {{ $path }}</option>@endforeach</select>
        <small>{{ __('A selected library image is reused; it is not copied or deleted.') }}</small>
    </div>
    <div class="admin-field full">
        <label for="gallery">{{ __('Additional company images') }}</label>
        @if(count($company->gallery ?? []))
            <div class="admin-company-gallery-current">
            @foreach($company->gallery ?? [] as $index => $path)
                <label class="admin-company-gallery-item"><img src="{{ \App\Support\EncapImage::url($path) }}" alt="" width="110" height="84" loading="lazy"><span><input type="checkbox" name="remove_gallery[]" value="{{ $index }}"> {{ __('Remove from this company') }}</span></label>
            @endforeach
            </div>
        @endif
        <input id="gallery" type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp,image/avif"><small>{{ __('Upload additional images for the company profile gallery, up to 12 images total.') }}</small>
    </div>
    <div class="admin-field full"><label for="library_gallery">{{ __('Or select images already in the central library') }}</label>
        <select id="library_gallery" name="library_gallery[]" multiple size="6">@foreach($mediaAssets as $path => $asset)<option value="{{ $path }}" @selected(in_array($path, old('library_gallery', []), true))>{{ $asset['label'] }} — {{ $path }}</option>@endforeach</select>
        <small>{{ __('Hold Ctrl or Command to select multiple images. Existing files remain in the library when removed from a company profile.') }}</small>
    </div>
    <div class="admin-field full"><label class="admin-check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $company->is_published))> {{ __('Publish this company on the public website') }}</label></div>
    <div class="admin-field full"><div class="admin-form-actions"><button type="submit">{{ $company->exists ? __('Save changes') : __('Create company') }}</button><a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('Cancel') }}</a></div></div>
</div></form>
@endsection
