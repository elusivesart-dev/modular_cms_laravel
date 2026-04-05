@extends('admin-theme::layouts.admin')

@section('title', __('settings::settings.create'))

@section('content')
<div class="card">
    <div class="card-header">
        <h5>{{ __('settings::settings.create') }}</h5>
    </div>

    <div class="card-block">
        <form method="POST" action="{{ route('settings.store') }}">
            @csrf

            @include('settings::settings.partials.form', [
                'setting' => null,
                'submitLabel' => __('settings::settings.save')
            ])
        </form>
    </div>
</div>
@endsection