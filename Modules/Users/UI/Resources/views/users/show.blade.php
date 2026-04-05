@extends('admin-theme::layouts.admin')

@section('title', __('users::users.show'))

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5>{{ __('users::users.show') }}</h5>
    </div>

    <div class="card-block">
        <p><strong>{{ __('users::users.name') }}:</strong> {{ $user->name }}</p>
        <p><strong>{{ __('users::users.email') }}:</strong> {{ $user->email }}</p>
        <p><strong>{{ __('users::users.is_active') }}:</strong> {{ $user->is_active ? __('core-localization::web.yes') : __('core-localization::web.no') }}</p>

        @isset($roles)
            <p>
                <strong>{{ __('users::users.roles') }}:</strong>
                @if($roles->isNotEmpty())
                    {{ $roles->pluck('name')->implode(', ') }}
                @else
                    —
                @endif
            </p>
        @endisset

        <div class="mt-3">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary waves-effect waves-light">
                {{ __('users::users.edit') }}
            </a>

            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                {{ __('users::users.back') }}
            </a>
        </div>
    </div>
</div>

@endsection