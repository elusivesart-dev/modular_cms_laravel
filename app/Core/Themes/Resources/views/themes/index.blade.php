@extends('admin-theme::layouts.admin')

@section('title', __('core-themes::themes.title'))

@section('content')
<div class="row">

    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    <!-- PUBLIC THEME -->
    <div class="col-xl-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('core-themes::themes.public_theme') }}</h5>
            </div>

            <div class="card-block">
                <form method="POST" action="{{ route('themes.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="group" value="public">

                    <div class="form-group form-primary">
                        <label>{{ __('core-themes::themes.active_theme') }}</label>
                        <select name="theme" class="form-control" required>
                            @foreach($publicThemes as $theme)
                                <option value="{{ $theme->slug }}"
                                    {{ old('group') === 'public'
                                        ? (old('theme') === $theme->slug ? 'selected' : '')
                                        : ($activePublicTheme->slug === $theme->slug ? 'selected' : '') }}>
                                    {{ $theme->name }} ({{ $theme->slug }})
                                </option>
                            @endforeach
                        </select>
                        <span class="form-bar"></span>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('core-themes::themes.current') }}:</strong>
                        {{ $activePublicTheme->name }} ({{ $activePublicTheme->slug }})
                    </div>

                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        {{ __('core-themes::themes.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ADMIN THEME -->
    <div class="col-xl-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('core-themes::themes.admin_theme') }}</h5>
            </div>

            <div class="card-block">
                <form method="POST" action="{{ route('themes.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="group" value="admin">

                    <div class="form-group form-primary">
                        <label>{{ __('core-themes::themes.active_theme') }}</label>
                        <select name="theme" class="form-control" required>
                            @foreach($adminThemes as $theme)
                                <option value="{{ $theme->slug }}"
                                    {{ old('group') === 'admin'
                                        ? (old('theme') === $theme->slug ? 'selected' : '')
                                        : ($activeAdminTheme->slug === $theme->slug ? 'selected' : '') }}>
                                    {{ $theme->name }} ({{ $theme->slug }})
                                </option>
                            @endforeach
                        </select>
                        <span class="form-bar"></span>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('core-themes::themes.current') }}:</strong>
                        {{ $activeAdminTheme->name }} ({{ $activeAdminTheme->slug }})
                    </div>

                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        {{ __('core-themes::themes.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection