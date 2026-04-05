@extends('public-theme::layouts.public')

@section('title', __('auth-module::auth.title'))

@push('styles')
    <style>
        .auth-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .auth-card .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 1.25rem 1.5rem;
        }

        .auth-card .card-body {
            padding: 1.5rem;
        }

        .auth-title {
            font-weight: 700;
            margin-bottom: 0;
        }

        .auth-subtitle {
            opacity: 0.8;
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .auth-form .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .auth-form .form-control {
            min-height: 48px;
            border-radius: 0.9rem;
        }

        .auth-actions .btn {
            min-height: 46px;
            border-radius: 0.9rem;
        }

        .auth-wrapper {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .auth-footer-link {
            text-decoration: none;
        }

        .auth-footer-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="auth-wrapper">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('success') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="{{ __('core-localization::web.close') }}"
                        ></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('info') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="{{ __('core-localization::web.close') }}"
                        ></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('error') }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="{{ __('core-localization::web.close') }}"
                        ></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        {{ $errors->first() }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="{{ __('core-localization::web.close') }}"
                        ></button>
                    </div>
                @endif

                <div class="card auth-card shadow-lg">
                    <div class="card-header bg-transparent">
                        <div class="d-flex flex-column gap-1">
                            <h3 class="auth-title">{{ __('auth-module::auth.title') }}</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('auth-module::auth.email') }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('auth-module::auth.password') }}</label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    autocomplete="current-password"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="form-check-input"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="remember">
                                    {{ __('auth-module::auth.remember') }}
                                </label>
                            </div>

                            <div class="d-grid gap-2 auth-actions">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth-module::auth.submit') }}
                                </button>

                                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                    {{ __('core-localization::web.back_to_website') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection