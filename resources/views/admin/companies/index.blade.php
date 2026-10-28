@extends('admin.layout')
@section('page-title', __('Group companies'))
@section('content')
<div class="admin-page-intro">
    <div><p>{{ __('Manage the companies displayed on the public group companies catalogue, including names, descriptions, locations and images.') }}</p></div>
    <div class="admin-actions"><a class="admin-btn" href="{{ route('admin.companies.create') }}">＋ {{ __('Add company') }}</a><a class="admin-btn secondary" href="{{ route('admin.media.index') }}">{{ __('Central image library') }}</a></div>
</div>
<div class="admin-panel">
    <div class="admin-panel-head"><div><h2>{{ __('Group company directory') }}</h2><p>{{ $companies->total() }} {{ __('companies in the directory') }}</p></div><span class="admin-muted">{{ __('Public content') }}</span></div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>{{ __('Company') }}</th><th>{{ __('Country / location') }}</th><th>{{ __('Visibility') }}</th><th>{{ __('Order') }}</th><th>{{ __('Actions') }}</th></tr></thead>
        <tbody>
        @forelse($companies as $company)
            <tr>
                <td><div class="admin-category-cell">
                    @if($company->image)<img class="admin-category-thumb" src="{{ \App\Support\EncapImage::url($company->image) }}" alt="" loading="lazy" width="52" height="52">@else<img class="admin-category-placeholder" src="{{ \App\Support\EncapImage::url('favicon.png') }}" alt="" aria-hidden="true" width="52" height="52">@endif
                    <div><strong>{{ app()->getLocale() === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en }}</strong><small class="admin-muted">{{ $company->slug }}</small><small class="admin-category-image-count">{{ ($company->image ? 1 : 0) + count($company->gallery ?? []) }} {{ __('images') }}</small></div>
                </div></td>
                <td>{{ app()->getLocale() === 'ar' && $company->location_ar ? $company->location_ar : ($company->location_en ?? '—') }}</td>
                <td><span class="admin-badge {{ $company->is_published ? '' : 'draft' }}">{{ __($company->is_published ? 'Published' : 'Draft') }}</span></td>
                <td>{{ $company->sort_order }}</td>
                <td><div class="admin-inline">
                    <a href="{{ route('admin.companies.edit', $company) }}">{{ __('Edit') }}</a>
                    <a href="{{ route('companies.show', ['locale' => app()->getLocale(), 'company' => $company->slug]) }}" target="_blank" rel="noopener">{{ __('View') }} ↗</a>
                    <form method="post" action="{{ route('admin.companies.destroy', $company) }}" onsubmit="return confirm('{{ __('Delete this company?') }}')">@csrf @method('DELETE')<button class="danger" type="submit">{{ __('Delete') }}</button></form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5"><p>{{ __('No companies yet. Add the first company to the group directory.') }}</p></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="pagination">{{ $companies->links() }}</div>
</div>
@endsection
