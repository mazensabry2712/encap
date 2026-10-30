@extends('admin.layout')
@php($selectedCategoryIds = collect(old('category_ids', $company->exists ? $company->categories->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())
@php($sectionRows = old('sections', is_array($company->content_sections) ? $company->content_sections : \App\Models\Company::defaultContentSections()))
@section('page-title', __('Edit company profile'))
@section('content')
<div class="admin-page-intro"><div><p>{{ __('Manage the public profile for Hadaeq Al-Shamal Company.') }}</p></div><div class="admin-actions"><a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('Company profile') }}</a></div></div>

<form class="admin-panel admin-form-panel" method="post" enctype="multipart/form-data" action="{{ route('admin.companies.update', $company) }}">
@csrf @method('PUT')
<div class="admin-form-grid">
    <h2 class="admin-section-title">{{ __('Basic information') }}</h2>
    <div class="admin-field full"><label>{{ __('Company') }}</label><p class="admin-company-profile-name">{{ app()->getLocale() === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en }}</p><small>{{ __('The company identity and public URL are fixed.') }}</small></div>
    <div class="admin-field full"><label for="short_description_en">{{ __('Short description (English)') }}</label><input id="short_description_en" name="short_description_en" value="{{ old('short_description_en', $company->short_description_en) }}"></div>
    <div class="admin-field full"><label for="short_description_ar">{{ __('Short description (Arabic)') }}</label><input id="short_description_ar" name="short_description_ar" dir="auto" value="{{ old('short_description_ar', $company->short_description_ar) }}"></div>
    <div class="admin-field"><label for="description_en">{{ __('Full description (English)') }}</label><textarea id="description_en" name="description_en">{{ old('description_en', $company->description_en) }}</textarea></div>
    <div class="admin-field"><label for="description_ar">{{ __('Full description (Arabic)') }}</label><textarea id="description_ar" name="description_ar" dir="auto">{{ old('description_ar', $company->description_ar) }}</textarea></div>
    <h2 class="admin-section-title">{{ __('Company location and links') }}</h2>
    <div class="admin-field"><label for="country_en">{{ __('Country (English)') }}</label><input id="country_en" name="country_en" value="{{ old('country_en', $company->country_en) }}"></div>
    <div class="admin-field"><label for="country_ar">{{ __('Country (Arabic)') }}</label><input id="country_ar" name="country_ar" dir="auto" value="{{ old('country_ar', $company->country_ar) }}"></div>
    <div class="admin-field"><label for="location_en">{{ __('Location (English)') }}</label><input id="location_en" name="location_en" value="{{ old('location_en', $company->location_en) }}"></div>
    <div class="admin-field"><label for="location_ar">{{ __('Location (Arabic)') }}</label><input id="location_ar" name="location_ar" dir="auto" value="{{ old('location_ar', $company->location_ar) }}"></div>
    <div class="admin-field full"><label for="website_url">{{ __('Company website URL') }}</label><input id="website_url" type="text" name="website_url" value="{{ old('website_url', $company->website_url) }}"></div>
    <h2 class="admin-section-title">{{ __('Company products') }}</h2>
    <div class="admin-field full">
        <input type="hidden" name="category_ids_present" value="1">
        <p class="admin-company-products-help">{{ __('Select the product categories this company supplies. The selected categories will appear on its public page.') }}</p>

        @if($categories->isEmpty())
            <p>{{ __('No product categories are available yet. Create categories in the Products section first.') }}</p>
        @else
            <div class="admin-company-products-grid">
                @foreach($categories as $category)
                    @php($categoryName = app()->getLocale() === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en)
                    <label class="admin-company-product-option">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array((int) $category->id, $selectedCategoryIds, true))>
                        <span>
                            <strong>{{ $categoryName }}</strong>
                            @if(!$category->is_published)
                                <small>{{ __('Unpublished') }}</small>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    <h2 class="admin-section-title">{{ __('Company photography') }}</h2>
    <div class="admin-field full">
        @if($company->image)<div class="admin-company-image-current"><img src="{{ \App\Support\EncapImage::url($company->image) }}" alt="{{ $company->name_en }}" width="180" height="140" loading="lazy"><span>{{ __('Current main image') }}</span></div>@endif
        <label for="image">{{ __('Upload main image') }}</label><input id="image" type="file" name="image" accept="image/*"><small>{{ __('Select an image file. A new upload replaces the current main image.') }}</small>
    </div>
    <div class="admin-field full">
        <label for="library_image">{{ __('Or choose an existing image from the central library') }}</label>
        <select id="library_image" name="library_image"><option value="">{{ __('Keep current image / use uploaded image') }}</option>@foreach($mediaAssets as $path => $asset)<option value="{{ $path }}" @selected(old('library_image') === $path)>{{ $asset['label'] }} — {{ $path }}</option>@endforeach</select>
        <small>{{ app()->getLocale() === 'ar' ? 'الصورة المختارة من المكتبة تُنسخ في ملف مستقل لهذا المكان داخل الشركة، دون تغيير النسخة الأصلية.' : 'A selected library image is copied into a separate file for this company location. The original remains unchanged.' }}</small>
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
        <input id="gallery" type="file" name="gallery[]" multiple accept="image/*"><small>{{ __('Upload as many additional images as needed. You can select multiple images at once.') }}</small>
    </div>
    <div class="admin-field full"><label for="library_gallery">{{ __('Or select images already in the central library') }}</label>
        <select id="library_gallery" name="library_gallery[]" multiple size="6">@foreach($mediaAssets as $path => $asset)<option value="{{ $path }}" @selected(in_array($path, old('library_gallery', []), true))>{{ $asset['label'] }} — {{ $path }}</option>@endforeach</select>
        <small>{{ __('Hold Ctrl or Command to select multiple images. Existing files remain in the library when removed from a company profile.') }}</small>
    </div>

    <h2 class="admin-section-title">{{ __('Company content sections') }}</h2>
    <div class="admin-field full">
        <p>{{ __('Add as many content sections and images as you need. Titles and images are optional; empty sections are skipped.') }}</p>
        <div class="admin-actions"><button class="admin-btn" type="button" data-add-company-section>{{ __('Add content section') }}</button></div>
    </div>
    <div class="admin-field full" data-company-section-list>
        <input type="hidden" name="sections_present" value="1">
        @foreach($sectionRows as $sectionIndex => $section)
            @php($sectionExistingImages = old('sections.'.$sectionIndex.'.existing_images', $section['existing_images'] ?? ($section['images'] ?? [])))
            <fieldset data-company-section-row style="margin:0 0 22px;padding:18px;border:1px solid #d6ddd3;border-radius:10px;min-width:0">
                <legend style="padding:0 8px;font-weight:700">{{ __('Content section') }} {{ $loop->iteration }}</legend>
                <div class="admin-form-grid">
                    <div class="admin-field">
                        <label>{{ __('Section title (Arabic)') }}</label>
                        <input name="sections[{{ $sectionIndex }}][title_ar]" dir="auto" value="{{ old('sections.'.$sectionIndex.'.title_ar', $section['title_ar'] ?? '') }}">
                    </div>
                    <div class="admin-field">
                        <label>{{ __('Section title (English)') }}</label>
                        <input name="sections[{{ $sectionIndex }}][title_en]" value="{{ old('sections.'.$sectionIndex.'.title_en', $section['title_en'] ?? '') }}">
                    </div>
                </div>
                <div class="admin-company-section-images" data-company-section-images>
                    @foreach($sectionExistingImages as $imagePosition => $imagePath)
                        @php($existingImagePath = old('sections.'.$sectionIndex.'.existing_images.'.$imagePosition, $imagePath))
                        <div class="admin-field admin-company-section-image" data-company-section-image-row>
                            <div class="admin-company-image-current">
                                @if(filled($existingImagePath))
                                    <img src="{{ \App\Support\EncapImage::url($existingImagePath) }}" alt="" width="180" height="120" loading="lazy">
                                    <span>{{ basename($existingImagePath) }}</span>
                                @endif
                            </div>
                            <input type="hidden" name="sections[{{ $sectionIndex }}][existing_images][{{ $imagePosition }}]" value="{{ $existingImagePath }}">
                            <label>{{ __('Choose from central image library') }}</label>
                            <select name="sections[{{ $sectionIndex }}][library_images][{{ $imagePosition }}]">
                                <option value="">{{ __('Keep current image unless uploading a replacement') }}</option>
                                @foreach($mediaAssets as $path => $asset)
                                    <option value="{{ $path }}" @selected(old('sections.'.$sectionIndex.'.library_images.'.$imagePosition) === $path)>{{ $asset['label'] }} — {{ $path }}</option>
                                @endforeach
                            </select>
                            <label>{{ __('Or upload a replacement image') }}</label>
                            <input type="file" name="sections[{{ $sectionIndex }}][upload_images][{{ $imagePosition }}]" accept="image/*">
                            <button type="button" class="admin-btn secondary" data-remove-company-section-image>{{ __('Remove image') }}</button>
                        </div>
                    @endforeach
                </div>
                <div class="admin-actions">
                    <button type="button" class="admin-btn secondary" data-add-company-section-image>{{ __('Add another image') }}</button>
                    <button type="button" class="admin-btn secondary" data-remove-company-section>{{ __('Remove this section') }}</button>
                </div>
            </fieldset>
        @endforeach

<template id="company-section-image-template">
        <div class="admin-field admin-company-section-image" data-company-section-image-row>
            <input type="hidden" name="sections[__INDEX__][existing_images][__IMAGE_INDEX__]" value="">
            <label>{{ __('Choose from central image library') }}</label>
            <select name="sections[__INDEX__][library_images][__IMAGE_INDEX__]">
                <option value="">{{ __('Select an image') }}</option>
                @foreach($mediaAssets as $path => $asset)
                    <option value="{{ $path }}">{{ $asset['label'] }} — {{ $path }}</option>
                @endforeach
            </select>
            <label>{{ __('Or upload an image') }}</label>
            <input type="file" name="sections[__INDEX__][upload_images][__IMAGE_INDEX__]" accept="image/*">
            <button type="button" class="admin-btn secondary" data-remove-company-section-image>{{ __('Remove image') }}</button>
        </div>
    </template>

    <template id="company-section-template">
        <fieldset data-company-section-row style="margin:0 0 22px;padding:18px;border:1px solid #d6ddd3;border-radius:10px;min-width:0">
            <legend style="padding:0 8px;font-weight:700">{{ __('New content section') }}</legend>
            <div class="admin-form-grid">
                <div class="admin-field">
                    <label>{{ __('Section title (Arabic)') }}</label>
                    <input name="sections[__INDEX__][title_ar]" dir="auto">
                </div>
                <div class="admin-field">
                    <label>{{ __('Section title (English)') }}</label>
                    <input name="sections[__INDEX__][title_en]">
                </div>
            </div>
            <div class="admin-company-section-images" data-company-section-images></div>
            <div class="admin-actions">
                <button type="button" class="admin-btn secondary" data-add-company-section-image>{{ __('Add another image') }}</button>
                <button type="button" class="admin-btn secondary" data-remove-company-section>{{ __('Remove this section') }}</button>
            </div>
        </fieldset>
    </template>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.querySelector('[data-company-section-list]');
        const sectionTemplate = document.getElementById('company-section-template');
        const imageTemplate = document.getElementById('company-section-image-template');
        const addButton = document.querySelector('[data-add-company-section]');
        if (!list || !sectionTemplate || !imageTemplate || !addButton) return;

        const renumber = function () {
            list.querySelectorAll('[data-company-section-row]').forEach(function (row, sectionIndex) {
                row.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace(/^sections\[(?:\d+|__INDEX__)\]/, 'sections[' + sectionIndex + ']');
                });
                row.querySelectorAll('[data-company-section-image-row]').forEach(function (imageRow, imageIndex) {
                    imageRow.querySelectorAll('[name]').forEach(function (input) {
                        input.name = input.name.replace(/\[(existing_images|library_images|upload_images)\]\[(?:\d+|__IMAGE_INDEX__)\]/, '[$1][' + imageIndex + ']');
                    });
                });
                const legend = row.querySelector('legend');
                if (legend) legend.textContent = @json(__('Content section')) + ' ' + (sectionIndex + 1);
            });
        };

        const addImage = function (row) {
            renumber();
            const sectionIndex = Array.from(list.querySelectorAll('[data-company-section-row]')).indexOf(row);
            const imageIndex = row.querySelectorAll('[data-company-section-image-row]').length;
            const markup = imageTemplate.innerHTML
                .replaceAll('__INDEX__', String(sectionIndex))
                .replaceAll('__IMAGE_INDEX__', String(imageIndex));
            row.querySelector('[data-company-section-images]').insertAdjacentHTML('beforeend', markup);
        };

        addButton.addEventListener('click', function () {
            const index = list.querySelectorAll('[data-company-section-row]').length;
            list.insertAdjacentHTML('beforeend', sectionTemplate.innerHTML.replaceAll('__INDEX__', String(index)));
            renumber();
        });

        list.addEventListener('click', function (event) {
            const addImageButton = event.target.closest('[data-add-company-section-image]');
            if (addImageButton) {
                const row = addImageButton.closest('[data-company-section-row]');
                if (row) addImage(row);
                return;
            }
            const removeImageButton = event.target.closest('[data-remove-company-section-image]');
            if (removeImageButton) {
                const imageRow = removeImageButton.closest('[data-company-section-image-row]');
                if (imageRow) imageRow.remove();
                renumber();
                return;
            }
            const removeSectionButton = event.target.closest('[data-remove-company-section]');
            if (removeSectionButton) {
                const row = removeSectionButton.closest('[data-company-section-row]');
                if (row) row.remove();
                renumber();
            }
        });
    });
    </script>



    <div class="admin-field full"><p class="admin-muted">{{ __('This profile is published directly at the company page.') }}</p></div>
    <div class="admin-field full"><div class="admin-form-actions"><button type="submit">{{ __('Save changes') }}</button><a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('Cancel') }}</a></div></div>
</div></form>
@endsection
