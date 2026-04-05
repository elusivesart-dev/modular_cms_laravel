@extends('admin-theme::layouts.admin')

@section('title', __('users::users.create'))

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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('core-localization::web.close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5>{{ __('users::users.create') }}</h5>
    </div>

    <div class="card-block">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="form-group form-primary">
                <label>{{ __('users::users.name') }}</label>
                <input name="name" class="form-control" value="{{ old('name') }}" required>
                <span class="form-bar"></span>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group form-primary">
                <label>{{ __('users::users.email') }}</label>
                <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                <span class="form-bar"></span>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group form-primary">
                <label>{{ __('users::users.password') }}</label>
                <input name="password" type="password" class="form-control" required>
                <span class="form-bar"></span>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label">{{ __('users::users.is_active') }}</label>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">{{ __('users::users.roles') }}</label>

                @foreach($roles as $role)
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="role_slugs[]"
                            value="{{ $role->slug }}"
                            class="form-check-input"
                            id="role_slug_{{ $role->slug }}"
                            {{ in_array($role->slug, old('role_slugs', []), true) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="role_slug_{{ $role->slug }}">{{ $role->name }}</label>
                    </div>
                @endforeach

                @error('role_slugs') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('role_slugs.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mt-3">
                <button class="btn btn-primary waves-effect waves-light">{{ __('users::users.save') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('users::users.back') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection