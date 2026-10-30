@extends('admin.layout')
@section('page-title', $category->exists ? __('Edit category') : __('Create category'))
@section('content')
@php($selectedCompanyIds = collect(old('company_ids', $category->exists ? $category->companies->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())
<div class="admin-page-intro"><div><p>{{ __('Maintain public product information, category imagery and search metadata. Publish only confirmed details.') }}</p></div><div class="admin-actions"><a class="admin-btn secondary" href="{{ route('admin.categories.index') }}">{{ __('← All categories') }}</a></div></div>
<form class="admin-panel admin-form-panel" method="post" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">@csrf @if($category->exists)@method('PUT')@endif<div class="admin-form-grid">
<h2 class="admin-section-title">{{ __('Basic information') }}</h2>
<div class="admin-field"><label for="name_en">{{ __('Name (English)') }}</label><input id="name_en" name="name_en" value="{{ old('name_en', $category->name_en) }}"></div><div class="admin-field"><label for="name_ar">{{ __('Name (Arabic)') }}</label><input id="name_ar" name="name_ar" dir="auto" value="{{ old('name_ar', $category->name_ar) }}"></div>
<div class="admin-field"><label for="slug">{{ __('URL slug (generated automatically when empty)') }}</label><input id="slug" name="slug" value="{{ old('slug', $category->slug) }}"><small>{{ __('Used in the public product URL. Keep it stable after publishing.') }}</small></div><div class="admin-field"><label for="sort_order">{{ __('Display order') }}</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"></div>
<div class="admin-field full"><label for="short_description_en">{{ __('Short description (English)') }}</label><input id="short_description_en" name="short_description_en" value="{{ old('short_description_en', $category->short_description_en) }}"></div><div class="admin-field full"><label for="short_description_ar">{{ __('Short description (Arabic)') }}</label><input id="short_description_ar" name="short_description_ar" dir="auto" value="{{ old('short_description_ar', $category->short_description_ar) }}"></div>
<div class="admin-field"><label for="description_en">{{ __('Full description (English)') }}</label><textarea id="description_en" name="description_en">{{ old('description_en', $category->description_en) }}</textarea></div><div class="admin-field"><label for="description_ar">{{ __('Full description (Arabic)') }}</label><textarea id="description_ar" name="description_ar" dir="auto">{{ old('description_ar', $category->description_ar) }}</textarea></div>
<h2 class="admin-section-title">{{ __('Product information') }}</h2>
<div class="admin-field"><label for="varieties_en">{{ __('Varieties (English)') }}</label><textarea id="varieties_en" name="varieties_en">{{ old('varieties_en', $category->varieties_en) }}</textarea></div><div class="admin-field"><label for="varieties_ar">{{ __('Varieties (Arabic)') }}</label><textarea id="varieties_ar" name="varieties_ar" dir="auto">{{ old('varieties_ar', $category->varieties_ar) }}</textarea></div>
<div class="admin-field"><label for="harvest_window_en">{{ __('Harvest window / season (English)') }}</label><input id="harvest_window_en" name="harvest_window_en" value="{{ old('harvest_window_en', $category->harvest_window_en) }}"></div><div class="admin-field"><label for="harvest_window_ar">{{ __('Harvest window / season (Arabic)') }}</label><input id="harvest_window_ar" name="harvest_window_ar" dir="auto" value="{{ old('harvest_window_ar', $category->harvest_window_ar) }}"></div>
<div class="admin-field"><label for="specifications_en">{{ __('Specifications, size or grade (English)') }}</label><textarea id="specifications_en" name="specifications_en">{{ old('specifications_en', $category->specifications_en) }}</textarea></div><div class="admin-field"><label for="specifications_ar">{{ __('Specifications, size or grade (Arabic)') }}</label><textarea id="specifications_ar" name="specifications_ar" dir="auto">{{ old('specifications_ar', $category->specifications_ar) }}</textarea></div>
<div class="admin-field"><label for="packaging_en">{{ __('Packaging (English)') }}</label><input id="packaging_en" name="packaging_en" value="{{ old('packaging_en', $category->packaging_en) }}"></div><div class="admin-field"><label for="packaging_ar">{{ __('Packaging (Arabic)') }}</label><input id="packaging_ar" name="packaging_ar" dir="auto" value="{{ old('packaging_ar', $category->packaging_ar) }}"></div>
<div class="admin-field full"><label>{{ __('Available formats') }}</label><div class="admin-inline">@foreach(['Fresh', 'Frozen', 'Processed'] as $format)<label class="admin-check"><input type="checkbox" name="available_forms[]" value="{{ $format }}" @checked(in_array($format, old('available_forms', $category->available_forms ?? [])))>{{ __($format) }}</label>@endforeach</div><small>{{ __('Available formats describe the form supplied; display locations are selected separately below.') }}</small></div>

<h2 class="admin-section-title">{{ __('Product placement') }}</h2>
<div class="admin-field full"><small>{{ __('Create and maintain each product here once. Select every public location where this same product should appear.') }}</small></div>
<div class="admin-field full"><label>{{ __('Public website locations') }}</label><div class="admin-inline admin-product-page-toggles">
    <label class="admin-check"><input type="checkbox" name="show_in_catalogue" value="1" @checked(old('show_in_catalogue', $category->exists ? $category->show_in_catalogue : true))> {{ __('Main product catalogue') }}</label>
    <label class="admin-check"><input type="checkbox" name="show_in_frozen_catalogue" value="1" @checked(old('show_in_frozen_catalogue', $category->show_in_frozen_catalogue ?? false))> {{ __('Frozen products catalogue') }}</label>
    <label class="admin-check"><input type="checkbox" name="show_on_homepage" value="1" @checked(old('show_on_homepage', $category->exists ? $category->show_on_homepage : true))> {{ __('Homepage products section') }}</label>
</div><small>{{ __('Showing a product in the frozen catalogue also enables the Frozen available format.') }}</small></div>
<div class="admin-field full"><label>{{ __('Company pages') }}</label><p class="admin-hint">{{ __('Select the company pages where this product should be listed. You can select more than one company.') }}</p>
    @if($companies->isEmpty())
        <p>{{ __('No companies are available yet. Create a company first.') }}</p>
    @else
        <div class="admin-company-products-grid">
            @foreach($companies as $company)
                @php($companyName = app()->getLocale() === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en)
                <label class="admin-company-product-option">
                    <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" @checked(in_array((int) $company->id, $selectedCompanyIds, true))>
                    <span><strong>{{ $companyName }}</strong>@if($company->location_en)<small>{{ app()->getLocale() === 'ar' && $company->location_ar ? $company->location_ar : $company->location_en }}</small>@endif</span>
                </label>
            @endforeach
        </div>
    @endif
</div>
<h2 class="admin-section-title">{{ __('Catalogue imagery') }}</h2>
@if($category->slug === 'mango')
<div class="admin-field full admin-mango-image-note">
    <div class="admin-company-image-current">
        <img src="{{ \App\Support\EncapImage::url('mango-replacement-2026.jpg', 'https://images.pexels.com/photos/29782357/pexels-photo-29782357/free-photo-of-ripe-mangoes-hanging-from-tree-in-tropical-setting.jpeg?auto=compress&w=1200') }}" alt="{{ __('Curated mango image') }}" width="180" height="140" loading="lazy">
        <div><strong>{{ __('Curated mango image') }}</strong><p>{{ __('The public mango photo is controlled from the central image library so the approved image stays consistent throughout the website.') }}</p>
        <a class="admin-panel-link" href="{{ route('admin.media.index', ['q' => 'mango-replacement-2026.jpg']) }}">{{ __('Open the central image library') }} ↗</a></div>
    </div>
</div>
@else
<div class="admin-field full admin-primary-image-field" data-main-image-manager data-current-gallery-count="{{ count($category->gallery ?? []) }}" data-text-no-replacement="{{ __('No product photos selected.') }}" data-text-batch-selected="{{ __(':count product photos selected.') }}" data-text-gallery-count="{{ __(':count will be added to the gallery.') }}" data-text-main-image-label="{{ __('Main image') }}" data-text-gallery-image-label="{{ __('Gallery image :number') }}">
    <div class="admin-media-field-heading">
        <div><label for="image">{{ __('Product photos') }}</label><p>{{ __('Select multiple product photos at once. The first becomes the main image; the rest are added to the gallery automatically.') }}</p></div>
        <span class="admin-media-tag">{{ __('Primary image') }}</span>
    </div>
    <div class="admin-primary-image-layout">
        <div class="admin-primary-image-preview" data-main-image-preview>
            @if($category->image)
                <img src="{{ \App\Support\EncapImage::url($category->image) }}" alt="{{ $category->name_en }}" data-main-image-current loading="lazy" width="360" height="270">
            @else
                <div class="admin-primary-image-empty" data-main-image-empty><span aria-hidden="true">＋</span><strong>{{ __('No main image yet') }}</strong></div>
            @endif
        </div>
        <div class="admin-primary-image-controls">
            <label class="admin-upload-label" for="image">{{ __('Choose product photos') }}</label>
            <input id="image" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/avif" data-main-image-upload>
            <small>{{ __('JPG, PNG, WebP or AVIF. The first selected photo becomes the main image; the rest are added to the gallery.') }}</small>
            <span class="admin-upload-status" data-main-image-status aria-live="polite">{{ __('No product photos selected.') }}</span>
            <div class="admin-gallery-selection admin-main-image-selection" data-main-image-selection hidden></div>
        </div>
    </div>
</div>

<div class="admin-field full admin-library-image-select">
    <label for="library_image">{{ __('Or use an existing image from the central library') }}</label>
    <select id="library_image" name="library_image">
        <option value="">{{ __('Use uploaded image / no existing selection') }}</option>
        @foreach($mediaAssets as $path => $asset)
            <option value="{{ $path }}" @selected(old('library_image') === $path)>{{ $asset['label'] }} — {{ $path }}</option>
        @endforeach
    </select>
    <small>{{ app()->getLocale() === 'ar' ? 'الصورة المختارة من المكتبة تُنسخ في ملف مستقل لهذا المنتج فقط، وتظل الصورة الأصلية كما هي.' : 'A library image is copied into a separate file for this product only. The original remains unchanged.' }}</small>
</div>

<div class="admin-field full">
    <label for="library_gallery">{{ __('Add existing images from the central library to this gallery') }}</label>
    <select id="library_gallery" name="library_gallery[]" multiple size="5">
        @foreach($mediaAssets as $path => $asset)
            <option value="{{ $path }}" @selected(in_array($path, old('library_gallery', []), true))>{{ $asset['label'] }} — {{ $path }}</option>
        @endforeach
    </select>
    <small>{{ __('Hold Ctrl or Command to select multiple images. The same library image can be reused by more than one product.') }}</small>
</div>

<div class="admin-field full">
    <div class="admin-gallery-workspace" data-gallery-manager data-existing-count="{{ count($category->gallery ?? []) }}" data-text-no-files="{{ __('No new images selected.') }}" data-text-selected="{{ __(':count images selected.') }}" data-text-mark-remove="{{ __('Mark for removal') }}" data-text-will-remove="{{ __('Will be removed') }}">
        <div class="admin-media-field-heading admin-gallery-heading">
            <div><label>{{ __('Additional product images') }}</label><p>{{ __('Add detail shots, packaging, harvest and product variations. These appear in a horizontal gallery on the public product page.') }}</p></div>
            <span class="admin-gallery-total" data-gallery-total>{{ count($category->gallery ?? []) }}</span>
        </div>

        @if(count($category->gallery ?? []))
            <div class="admin-gallery-manager" data-gallery-current>
                @foreach($category->gallery as $index => $image)
                    <article class="admin-gallery-item">
                        <div class="admin-gallery-image">
                            <img src="{{ \App\Support\EncapImage::url($image) }}" alt="{{ __('Gallery image :number', ['number' => $loop->iteration]) }}" loading="lazy" width="320" height="240">
                            <span class="admin-gallery-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="admin-gallery-item-details">
                            <strong>{{ __('Gallery image :number', ['number' => $loop->iteration]) }}</strong>
                            <small title="{{ basename($image) }}">{{ basename($image) }}</small>
                            <label class="admin-gallery-remove"><input type="checkbox" class="js-gallery-remove" name="remove_gallery[]" value="{{ $index }}"><span>{{ __('Mark for removal') }}</span></label>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="admin-gallery-empty" data-gallery-empty>
                <span aria-hidden="true">▧</span>
                <strong>{{ __('No additional images yet') }}</strong>
                <p>{{ __('The main image is separate. Add extra photos here to build the product gallery.') }}</p>
            </div>
        @endif

        <div class="admin-gallery-upload">
            <div class="admin-gallery-upload-heading">
                <div><strong>{{ __('Add more images') }}</strong><p>{{ __('Select multiple files at once. Add as many additional images as needed.') }}</p></div>
            </div>
            <input id="gallery" type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp,image/avif" data-gallery-upload>
            <div class="admin-gallery-selection" data-gallery-selection hidden></div>
            <div class="admin-gallery-upload-status" data-gallery-status role="status" aria-live="polite">{{ __('No new images selected.') }}</div>
        </div>
    </div>
</div>
@endif

<h2 class="admin-section-title">{{ __('Public product detail page') }}</h2>
<div class="admin-field full"><small>{{ __('Control the copy and visibility of sections on this category\'s public page. Leave custom headings or messages blank to use the site\'s default text.') }}</small></div>
<div class="admin-field"><label for="overview_heading_en">{{ __('Product overview heading (English)') }}</label><input id="overview_heading_en" name="overview_heading_en" value="{{ old('overview_heading_en', $category->overview_heading_en) }}"></div><div class="admin-field"><label for="overview_heading_ar">{{ __('Product overview heading (Arabic)') }}</label><input id="overview_heading_ar" name="overview_heading_ar" dir="auto" value="{{ old('overview_heading_ar', $category->overview_heading_ar) }}"></div>
<div class="admin-field"><label for="enquiry_note_heading_en">{{ __('Enquiry note heading (English)') }}</label><input id="enquiry_note_heading_en" name="enquiry_note_heading_en" value="{{ old('enquiry_note_heading_en', $category->enquiry_note_heading_en) }}"></div><div class="admin-field"><label for="enquiry_note_heading_ar">{{ __('Enquiry note heading (Arabic)') }}</label><input id="enquiry_note_heading_ar" name="enquiry_note_heading_ar" dir="auto" value="{{ old('enquiry_note_heading_ar', $category->enquiry_note_heading_ar) }}"></div>
<div class="admin-field"><label for="enquiry_note_text_en">{{ __('Enquiry note text (English)') }}</label><textarea id="enquiry_note_text_en" name="enquiry_note_text_en">{{ old('enquiry_note_text_en', $category->enquiry_note_text_en) }}</textarea></div><div class="admin-field"><label for="enquiry_note_text_ar">{{ __('Enquiry note text (Arabic)') }}</label><textarea id="enquiry_note_text_ar" name="enquiry_note_text_ar" dir="auto">{{ old('enquiry_note_text_ar', $category->enquiry_note_text_ar) }}</textarea></div>
<div class="admin-field"><label for="bottom_cta_heading_en">{{ __('Bottom call-to-action heading (English)') }}</label><input id="bottom_cta_heading_en" name="bottom_cta_heading_en" value="{{ old('bottom_cta_heading_en', $category->bottom_cta_heading_en) }}"></div><div class="admin-field"><label for="bottom_cta_heading_ar">{{ __('Bottom call-to-action heading (Arabic)') }}</label><input id="bottom_cta_heading_ar" name="bottom_cta_heading_ar" dir="auto" value="{{ old('bottom_cta_heading_ar', $category->bottom_cta_heading_ar) }}"></div>
<div class="admin-field"><label for="bottom_cta_text_en">{{ __('Bottom call-to-action text (English)') }}</label><textarea id="bottom_cta_text_en" name="bottom_cta_text_en">{{ old('bottom_cta_text_en', $category->bottom_cta_text_en) }}</textarea></div><div class="admin-field"><label for="bottom_cta_text_ar">{{ __('Bottom call-to-action text (Arabic)') }}</label><textarea id="bottom_cta_text_ar" name="bottom_cta_text_ar" dir="auto">{{ old('bottom_cta_text_ar', $category->bottom_cta_text_ar) }}</textarea></div>
<div class="admin-field full"><label>{{ __('Sections shown on the public product page') }}</label><div class="admin-inline admin-product-page-toggles">
@foreach(['show_product_images' => 'Main image and gallery', 'show_enquiry_note' => 'Enquiry note beside the title', 'show_description' => 'Product overview and description', 'show_available_forms' => 'Available formats', 'show_packaging' => 'Packaging details', 'show_specifications' => 'Varieties, season and specifications', 'show_bottom_cta' => 'Bottom quote call-to-action'] as $flag => $label)
<label class="admin-check"><input type="checkbox" name="{{ $flag }}" value="1" @checked(old($flag, $category->exists ? $category->{$flag} : true))>{{ __($label) }}</label>
@endforeach
</div></div>
<h2 class="admin-section-title">{{ __('Search engine metadata') }}</h2><div class="admin-field"><label for="seo_title_en">{{ __('SEO title (English)') }}</label><input id="seo_title_en" name="seo_title_en" value="{{ old('seo_title_en', $category->seo_title_en) }}"></div><div class="admin-field"><label for="seo_title_ar">{{ __('SEO title (Arabic)') }}</label><input id="seo_title_ar" name="seo_title_ar" dir="auto" value="{{ old('seo_title_ar', $category->seo_title_ar) }}"></div><div class="admin-field"><label for="seo_description_en">{{ __('SEO description (English)') }}</label><textarea id="seo_description_en" name="seo_description_en">{{ old('seo_description_en', $category->seo_description_en) }}</textarea></div><div class="admin-field"><label for="seo_description_ar">{{ __('SEO description (Arabic)') }}</label><textarea id="seo_description_ar" name="seo_description_ar" dir="auto">{{ old('seo_description_ar', $category->seo_description_ar) }}</textarea></div>
<div class="admin-field full"><label class="admin-check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $category->is_published))> {{ __('Publish this product on the public website') }}</label></div><div class="admin-field full"><div class="admin-form-actions"><button type="submit">{{ $category->exists ? __('Save changes') : __('Create category') }}</button><a class="admin-btn secondary" href="{{ route('admin.categories.index') }}">{{ __('Cancel') }}</a></div></div>
</div></form>
@endsection
