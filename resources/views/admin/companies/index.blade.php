@extends('admin.layout')
@section('page-title', __('Company profile'))
@section('content')
<div class="admin-page-intro">
    <div><p>{{ __('Manage the public profile for Hadaeq Al-Shamal Company.') }}</p></div>
    <div class="admin-actions">
        <a class="admin-btn" href="{{ route('admin.companies.edit', $company) }}">{{ __('Edit company profile') }}</a>
        <a class="admin-btn secondary" href="{{ route('admin.media.index') }}">{{ __('Central image library') }}</a>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>{{ app()->getLocale() === 'ar' && $company->name_ar ? $company->name_ar : $company->name_en }}</h2>
            <p>{{ __('The public page shows only Hadaeq Al-Shamal Company.') }}</p>
        </div>
        <span class="admin-badge">{{ __('Published') }}</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>{{ __('Country / location') }}</th><th>{{ __('Images') }}</th><th>{{ __('Public page') }}</th></tr></thead>
            <tbody><tr>
                <td>{{ app()->getLocale() === 'ar' && $company->location_ar ? $company->location_ar : ($company->location_en ?? '—') }}</td>
                <td>{{ ($company->image ? 1 : 0) + count($company->gallery ?? []) }}</td>
                <td><a href="{{ route('companies', ['locale' => app()->getLocale()]) }}" target="_blank" rel="noopener">{{ __('View public profile') }} ↗</a></td>
            </tr></tbody>
        </table>
    </div>
</div>
@endsection
