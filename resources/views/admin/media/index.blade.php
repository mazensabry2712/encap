@extends('admin.layout')
@section('page-title', __('Central image library'))

@section('content')
<div class="admin-page-intro">
    <div><p>{{ app()->getLocale() === 'ar' ? 'كل استخدام للصورة له مكان مستقل. استبدال صورة المنتج أو الشركة يغيّر هذا المكان فقط، ولا يلمس أي مكان آخر يستخدم الصورة الأصلية.' : 'Each image use has its own location. Replacing a product or company image changes only that location and leaves every other use untouched.' }}</p></div>
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
                    @if(count($asset['usages']))
                        <div class="admin-media-usage-list">
                            @foreach($asset['usages'] as $usage)
                                <div class="admin-media-usage-item">
                                    <p class="admin-media-usage"><strong>{{ app()->getLocale() === 'ar' ? 'المكان:' : 'Location:' }}</strong> {{ $usage['location'] }}</p>
                                    <form class="admin-media-replace-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.replace') }}">
                                        @csrf
                                        <input type="hidden" name="path" value="{{ $asset['path'] }}">
                                        <input type="hidden" name="usage" value="{{ $usage['key'] }}">
                                        <label for="replace-{{ md5($asset['path'].'-'.$usage['key']) }}">{{ app()->getLocale() === 'ar' ? 'استبدال الصورة في هذا المكان فقط' : 'Replace only in this location' }}</label>
                                        <input id="replace-{{ md5($asset['path'].'-'.$usage['key']) }}" type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/avif,image/gif">
                                        <small>{{ app()->getLocale() === 'ar' ? 'سيتم إنشاء ملف مستقل لهذا المكان.' : 'A separate image file will be created for this location.' }}</small>
                                        <button class="admin-btn secondary" type="submit">{{ app()->getLocale() === 'ar' ? 'استبدال هنا فقط' : 'Replace here only' }}</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @elseif($asset['global_brand_asset'])
                        <form class="admin-media-replace-form" method="post" enctype="multipart/form-data" action="{{ route('admin.media.replace') }}">
                            @csrf
                            <input type="hidden" name="path" value="{{ $asset['path'] }}">
                            <label for="replace-{{ md5($asset['path']) }}">{{ __('Replace this image') }}</label>
                            <input id="replace-{{ md5($asset['path']) }}" type="file" name="image" required accept="image/png">
                            <small>{{ app()->getLocale() === 'ar' ? 'يتم استخدام شعار وهوية الموقع بشكل موحّد في عناصر العلامة التجارية.' : 'Brand marks remain consistent across the site identity.' }}</small>
                            <button class="admin-btn secondary" type="submit">{{ __('Replace image') }}</button>
                        </form>
                    @else
                        <p class="admin-media-usage">{{ app()->getLocale() === 'ar' ? 'هذه الصورة متاحة في المكتبة للاختيار من داخل صفحة المنتج أو الشركة، ولم يتم ربطها بمكان قابل للاستبدال المباشر.' : 'This image is available in the library to assign from a product or company editor. It is not attached to one editable location yet.' }}</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="admin-dashboard-empty"><strong>{{ __('No image assets matched this search.') }}</strong></div>
        @endforelse
    </div>
</section>
@endsection
