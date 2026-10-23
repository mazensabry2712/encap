{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
@php($baseUrl = rtrim(config('app.url'), '/'))
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach(['en', 'ar', 'fr', 'ru', 'tr'] as $locale)
    @foreach(['' => ['priority' => '1.0', 'changefreq' => 'weekly'], '/products' => ['priority' => '0.8', 'changefreq' => 'weekly'], '/about' => ['priority' => '0.6', 'changefreq' => 'monthly'], '/farms' => ['priority' => '0.6', 'changefreq' => 'monthly'], '/packing-stations' => ['priority' => '0.6', 'changefreq' => 'monthly'], '/quality' => ['priority' => '0.5', 'changefreq' => 'monthly'], '/export' => ['priority' => '0.6', 'changefreq' => 'monthly'], '/contact' => ['priority' => '0.5', 'changefreq' => 'monthly']] as $path => $metadata)
    <url>
        <loc>{{ $baseUrl . '/' . $locale . $path }}</loc>
        @foreach(['en', 'ar', 'fr', 'ru', 'tr'] as $alternateLocale)
        <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $baseUrl . '/' . $alternateLocale . $path }}"/>
        @endforeach
        <changefreq>{{ $metadata['changefreq'] }}</changefreq>
        <priority>{{ $metadata['priority'] }}</priority>
    </url>
    @endforeach
    @foreach($categories as $category)
        <url>
            <loc>{{ $baseUrl . '/' . $locale . '/products/' . $category->slug }}</loc>
            <lastmod>{{ optional($category->updated_at)->toAtomString() }}</lastmod>
            @foreach(['en', 'ar', 'fr', 'ru', 'tr'] as $alternateLocale)
            <xhtml:link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ $baseUrl . '/' . $alternateLocale . '/products/' . $category->slug }}"/>
            @endforeach
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
@endforeach
</urlset>
