@extends('admin.layout')
@section('page-title', __('Product categories'))
@section('content')
<div class="admin-page-intro"><div><p>{{ __('Control which products appear in the public catalogue and keep category information current.') }}</p></div><div class="admin-actions"><a class="admin-btn" href="{{ route('admin.categories.create') }}">＋ {{ __('Add category') }}</a></div></div>
<div class="admin-panel"><div class="admin-panel-head"><div><h2>{{ __('Catalogue overview') }}</h2><p>{{ $categories->total() }} {{ __('categories in the catalogue') }}</p></div><span class="admin-muted">{{ __('Public content') }}</span></div>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('Category') }}</th><th>{{ __('Slug') }}</th><th>{{ __('Available formats') }}</th><th>{{ __('Where it appears') }}</th><th>{{ __('Visibility') }}</th><th>{{ __('Order') }}</th><th>{{ __('Actions') }}</th></tr></thead><tbody>
@forelse($categories as $category)<tr><td><div class="admin-category-cell">@if($category->image)<img class="admin-category-thumb" src="{{ \App\Support\EncapImage::url($category->image) }}" alt="" loading="lazy" width="52" height="52">@else<img class="admin-category-placeholder" src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" aria-hidden="true" width="52" height="52" loading="lazy" decoding="async">@endif<div><strong>{{ app()->getLocale() === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en }}</strong><small class="admin-muted">{{ app()->getLocale() === 'ar' ? $category->name_en : ($category->name_ar ?: __('Name (Arabic)')) }}</small><small class="admin-category-image-count">{{ ($category->image ? 1 : 0) + count($category->gallery ?? []) }} {{ __('images') }}</small></div></div></td><td>{{ $category->slug }}</td><td>@if(!empty($category->available_forms)){{ collect($category->available_forms)->map(fn ($format) => __($format))->implode(', ') }}@else<span class="admin-muted">{{ __('No format selected') }}</span>@endif</td><td>
@php($placements = collect([
    $category->show_in_catalogue ? __('Products') : null,
    $category->show_in_frozen_catalogue ? __('Frozen') : null,
    $category->show_on_homepage ? __('Home') : null,
    $category->companies->isNotEmpty() ? __('Company pages') : null,
])->filter())
@if($placements->isNotEmpty()){{ $placements->implode(' · ') }}@else<span class="admin-muted">{{ __('No display locations selected') }}</span>@endif
</td><td><span class="admin-badge {{ $category->is_published ? '' : 'draft' }}">{{ __($category->is_published ? 'Published' : 'Draft') }}</span></td><td>{{ $category->sort_order }}</td><td><div class="admin-inline"><a href="{{ route('admin.categories.edit', $category) }}">{{ __('Edit') }}</a><a href="{{ route('products.show', ['locale' => app()->getLocale(), 'category' => $category->slug]) }}" target="_blank" rel="noopener">{{ __('View') }} ↗</a><form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('Delete this category?') }}')">@csrf @method('DELETE')<button class="danger" type="submit">{{ __('Delete') }}</button></form></div></td></tr>
@empty<tr><td colspan="7"><p>{{ __('No categories yet. Add the first category to begin building the public catalogue.') }}</p></td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $categories->links() }}</div></div>
@endsection
