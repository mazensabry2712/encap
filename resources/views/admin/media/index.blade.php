@extends('admin.layout')
@section('page-title', __('Central image library'))

@section('content')
<div class="admin-page-intro">
    <div><p>{{ __('Manage images used across the public website from one place. Replacing a named asset keeps its URL so the same image updates wherever the site uses it.') }}</p></div>
    <div class="admin-actions"><a class="admin-btn secondary" href="{{ route('admin.categories.index') }}">{{ __('Product catalogue') }}</a><a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('Group companies') }}</a></div>
</div>

<section class="admin-panel admin-media-upload-panel" aria-labelledby="media-upload-title">
    <div class="admin-panel-head">
        <div><h2 id="media-upload-title">{{ __('Add an image to the central library') }}</h2><p>{{ __('New uploads are saved outside the public folder and can be assigned while editing product or company records.') }}</p></div>
    </div>
    <form class="admin-media-upload-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.upload') }}">
        @csrf
        <div class="admin-field"><label for="media-label">{{ __('Image name') }}</label><input id="media-label" name="label" required maxlength="100" placeholder="{{ __('For example: New farm overview') }}"></div>
        <div class="admin-field"><label for="media-upload">{{ __('Choose image') }}</label><input id="media-upload" type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/avif,image/gif"><small>{{ __('JPG, PNG, WebP, AVIF or GIF · maximum 5 MB.') }}</small></div>
        <div class="admin-field"><button class="admin-btn" type="submit">{{ __('Upload to library') }} ↗</button></div>
    </form>
</section>

<section class="admin-panel" aria-labelledby="media-library-title">
    <div class="admin-panel-head">
        <div><h2 id="media-library-title">{{ __('All site images') }}</h2><p>{{ number_format($totalAssets) }} {{ __('image assets and replaceable slots') }}</p></div>
        <form method="get" action="{{ route('admin.media.index') }}" class="admin-media-search">
            <label class="sr-only" for="media-query">{{ __('Search image path') }}</label>
            <input id="media-query" name="q" type="search" value="{{ $query }}" placeholder="{{ __('Search by filename or path') }}">
            <button class="admin-btn secondary" type="submit">{{ __('Search') }}</button>
            @if($query !== '')<a class="admin-panel-link" href="{{ route('admin.media.index') }}">{{ __('Clear') }}</a>@endif
        </form>
    </div>

    <div class="admin-media-grid">
        @forelse($assets as $asset)
            <article class="admin-media-card">
                <div class="admin-media-preview">
                    <img src="{{ $asset['url'] }}" alt="{{ $asset['label'] }}" loading="lazy" decoding="async">
                    @if($asset['is_slot'])<span class="admin-media-slot-badge">{{ __('Site asset') }}</span>@endif
                </div>
                <div class="admin-media-card-body">
                    <h3>{{ $asset['label'] }}</h3>
                    <code>{{ $asset['path'] }}</code>
                    @if(count($asset['categories']) || count($asset['companies']) || $asset['seo'])
                        <p class="admin-media-usage">
                            @if(count($asset['categories'])){{ __('Products') }}: {{ implode(', ', $asset['categories']) }}@endif
                            @if(count($asset['companies'])){{ count($asset['categories']) ? ' · ' : '' }}{{ __('Companies') }}: {{ implode(', ', $asset['companies']) }}@endif
                            @if($asset['seo']){{ (count($asset['categories']) || count($asset['companies'])) ? ' · ' : '' }}{{ __('SEO and social sharing') }}@endif
                        </p>
                    @endif
                    <div class="admin-media-source-line">
                        <span class="admin-badge {{ $asset['in_main'] ? '' : 'draft' }}">{{ $asset['in_main'] ? __('Main image folder') : ($asset['in_legacy'] ? __('Legacy image folder') : __('Fallback / missing file')) }}</span>
                        @if($asset['in_main'])<a href="{{ $asset['url'] }}" target="_blank" rel="noopener">{{ __('Open') }} ↗</a>@endif
                    </div>
                    <form class="admin-media-replace-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.replace') }}">
                        @csrf
                        <input type="hidden" name="path" value="{{ $asset['path'] }}">
                        <label for="replace-{{ md5($asset['path']) }}">{{ __('Replace this image') }}</label>
                        <input id="replace-{{ md5($asset['path']) }}" type="file" name="image" required accept="{{ str_ends_with(strtolower($asset['path']), '.png') ? 'image/png' : (str_ends_with(strtolower($asset['path']), '.webp') ? 'image/webp' : (str_ends_with(strtolower($asset['path']), '.avif') ? 'image/avif' : (str_ends_with(strtolower($asset['path']), '.gif') ? 'image/gif' : 'image/jpeg'))) }}">
                        <small>{{ __('Use the same file format as the target image.') }}</small>
                        <button class="admin-btn secondary" type="submit">{{ __('Replace image') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="admin-dashboard-empty"><strong>{{ __('No image assets matched this search.') }}</strong></div>
        @endforelse
    </div>
</section>
@endsection
