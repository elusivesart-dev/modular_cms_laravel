@extends('admin-theme::layouts.admin')

@section('title', __('settings::settings.edit'))

@section('content')
<div class="card">
    <div class="card-header">
        <h5>{{ __('settings::settings.edit') }}</h5>
    </div>

    <div class="card-block">
        <form method="POST" action="{{ route('settings.update', $setting) }}">
            @csrf
            @method('PUT')

            @include('settings::settings.partials.form', [
                'setting' => $setting,
                'submitLabel' => __('settings::settings.update')
            ])
        </form>
    </div>
</div>
@endsection