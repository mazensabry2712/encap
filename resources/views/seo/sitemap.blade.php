{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
@php($baseUrl = rtrim(config('app.url'), '/'))
@php($locales = $locales ?? ['en', 'ar', 'fr', 'ru', 'tr'])
@php($pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'weekly'],
    '/products' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    '/about' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/companies' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/farms' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/packing-stations' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/quality' => ['priority' => '0.5', 'changefreq' => 'monthly'],
    '/export' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/contact' => ['priority' => '0.5', 'changefreq' => 'monthly'],
])
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($locales as $locale)
    @foreach($pages as $path => $metadata)
    <url>
        <loc>{{ $baseUrl . '/' . $locale . $path }}</loc>
        @foreach($locales as $alternateLocale)
        <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $baseUrl . '/' . $alternateLocale . $path }}"/>
        @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $baseUrl . '/en' . $path }}"/>
        <changefreq>{{ $metadata['changefreq'] }}</changefreq>
        <priority>{{ $metadata['priority'] }}</priority>
    </url>
    @endforeach
    @foreach($categories as $category)
        @if($locale !== 'ar' || ($category->name_ar && $category->description_ar))
        <url>
            <loc>{{ $baseUrl . '/' . $locale . '/products/' . $category->slug }}</loc>
            <lastmod>{{ optional($category->updated_at)->toAtomString() }}</lastmod>
            @foreach($locales as $alternateLocale)
                @if($alternateLocale !== 'ar' || ($category->name_ar && $category->description_ar))
            <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $baseUrl . '/' . $alternateLocale . '/products/' . $category->slug }}"/>
                @endif
            @endforeach
            <xhtml:link rel="alternate" hreflang="x-default" href="{{ $baseUrl . '/en/products/' . $category->slug }}"/>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
        @endif
    @endforeach
    @foreach(($companies ?? collect()) as $company)
        <url>
            <loc>{{ $baseUrl . '/' . $locale . '/companies/' . $company->slug }}</loc>
            <lastmod>{{ optional($company->updated_at)->toAtomString() }}</lastmod>
            @foreach($locales as $alternateLocale)
            <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $baseUrl . '/' . $alternateLocale . '/companies/' . $company->slug }}"/>
            @endforeach
            <xhtml:link rel="alternate" hreflang="x-default" href="{{ $baseUrl . '/en/companies/' . $company->slug }}"/>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
@endforeach
</urlset>
