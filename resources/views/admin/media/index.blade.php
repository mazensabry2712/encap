@extends('admin.layout')
@section('page-title', __('Central image library'))

@section('content')
<div class="admin-page-intro">
    <div>
        <p>{{ __('Images are arranged by the page and exact place where they appear. Choose the location name you want to change; only that image will be replaced.') }}</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn secondary" href="{{ route('admin.categories.index') }}">{{ __('Product catalogue') }}</a>
        <a class="admin-btn secondary" href="{{ route('admin.companies.index') }}">{{ __('Group companies') }}</a>
    </div>
</div>

<section class="admin-panel admin-media-upload-panel" aria-labelledby="media-upload-title">
    <div class="admin-panel-head">
        <div>
            <h2 id="media-upload-title">{{ __('Add an image to the reusable library') }}</h2>
            <p>{{ __('Upload an image here to use it later from a product or company editor. Give it a clear name so you can find it again.') }}</p>
        </div>
    </div>
    <form class="admin-media-upload-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.upload') }}">
        @csrf
        <div class="admin-field">
            <label for="media-label">{{ __('Image name') }}</label>
            <input id="media-label" name="label" required maxlength="100" placeholder="{{ __('For example: New farm overview') }}">
        </div>
        <div class="admin-field">
            <label for="media-upload">{{ __('Choose image') }}</label>
            <input id="media-upload" type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/avif,image/gif">
            <small>{{ __('JPG, PNG, WebP, AVIF or GIF · maximum 5 MB.') }}</small>
        </div>
        <div class="admin-field">
            <button class="admin-btn" type="submit">{{ __('Upload image') }} ↗</button>
        </div>
    </form>
</section>

<section class="admin-panel admin-media-locations-panel" aria-labelledby="media-library-title">
    <div class="admin-panel-head admin-media-library-head">
        <div>
            <h2 id="media-library-title">{{ __('Images by website location') }}</h2>
            <p>{{ number_format($placementCount) }} {{ __('image locations') }} · {{ number_format($totalAssets) }} {{ __('files in storage') }}</p>
        </div>
        <form method="get" action="{{ route('admin.media.index') }}" class="admin-media-search">
            <label class="sr-only" for="media-query">{{ __('Search image locations') }}</label>
            <input id="media-query" name="q" type="search" value="{{ $query }}" placeholder="{{ __('Search by page, location or filename') }}">
            <button class="admin-btn secondary" type="submit">{{ __('Search') }}</button>
            @if($query !== '')
                <a class="admin-panel-link" href="{{ route('admin.media.index') }}">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    @forelse($groups as $groupName => $items)
        <section class="admin-media-group" data-media-group="{{ $groupName }}" aria-labelledby="media-group-{{ $loop->index }}">
            <div class="admin-media-group-heading">
                <h3 id="media-group-{{ $loop->index }}">{{ $groupName }}</h3>
                <span>{{ $items->count() }} {{ __('locations') }}</span>
            </div>
            <div class="admin-media-grid">
                @foreach($items as $asset)
                    <article class="admin-media-card">
                        <div class="admin-media-preview">
                            <img src="{{ $asset['url'] }}" alt="{{ $asset['location'] }}" loading="lazy" decoding="async">
                            <span class="admin-media-slot-badge">{{ __('Current location') }}</span>
                        </div>
                        <div class="admin-media-card-body">
                            <h4>{{ $asset['location'] }}</h4>
                            <p class="admin-media-file-label">{{ __('Current image') }}: {{ $asset['label'] }}</p>
                            <code>{{ $asset['path'] }}</code>

                            <form class="admin-media-replace-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.replace') }}">
                                @csrf
                                <input type="hidden" name="path" value="{{ $asset['path'] }}">
                                @if($asset['usage_key'])
                                    <input type="hidden" name="usage" value="{{ $asset['usage_key'] }}">
                                @endif
                                <label for="replace-{{ md5($asset['path'].'-'.($asset['usage_key'] ?? 'brand')) }}">{{ __('Replace image in this place') }}</label>
                                <input id="replace-{{ md5($asset['path'].'-'.($asset['usage_key'] ?? 'brand')) }}" type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/avif,image/gif">
                                <small>{{ __('Only this location changes. Other pages using the same original image stay unchanged.') }}</small>
                                <button class="admin-btn secondary" type="submit">{{ __('Replace this image') }}</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="admin-dashboard-empty">
            <strong>{{ __('No image locations matched this search.') }}</strong>
            <p>{{ __('Try another page name, location title or image filename.') }}</p>
        </div>
    @endforelse
</section>

@if($unusedAssets->isNotEmpty())
    <details class="admin-panel admin-media-unused" @if($query !== '') open @endif>
        <summary>
            <span>
                <strong>{{ __('Additional images not assigned to a page') }}</strong>
                <small>{{ __('These uploads are not currently connected to a visible website location. Delete only the ones you no longer need.') }}</small>
            </span>
            <span class="admin-media-unused-count">{{ $unusedAssets->count() }} {{ __('images') }}</span>
        </summary>
        <div class="admin-media-grid">
            @foreach($unusedAssets as $asset)
                <article class="admin-media-card admin-media-unused-card">
                    <div class="admin-media-preview">
                        <img src="{{ $asset['url'] }}" alt="{{ $asset['label'] }}" loading="lazy" decoding="async">
                    </div>
                    <div class="admin-media-card-body">
                        <h4>{{ $asset['label'] }}</h4>
                        <code>{{ $asset['path'] }}</code>
                        <form method="post" action="{{ route('admin.media.delete-unused') }}" onsubmit="return confirm('{{ __('Delete this unused image permanently?') }}')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="path" value="{{ $asset['path'] }}">
                            <button class="admin-btn danger" type="submit">{{ __('Delete unused image') }}</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </details>
@endif
@endsection
