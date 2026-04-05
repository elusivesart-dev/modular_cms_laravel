@extends('public-theme::layouts.public')

@section('title', __('users::users.public.register'))

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

        @if (!empty($captchaEnabled) && !empty($captchaSiteKey))
            .grecaptcha-badge {
                visibility: visible !important;
                opacity: 1 !important;
                pointer-events: auto !important;
                z-index: 2147483647 !important;
            }
        @endif
    </style>
@endpush

@section('content')
    <div class="auth-wrapper">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-12">
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
                            <h3 class="auth-title">{{ __('users::users.public.register') }}</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register.store') }}" id="register-form" class="auth-form" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('users::users.name') }}</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value=""
                                    maxlength="120"
                                    autocomplete="name"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('users::users.email') }}</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value=""
                                    maxlength="255"
                                    autocomplete="email"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('users::users.password') }}</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    autocomplete="new-password"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    {{ __('users::users.public.password_confirmation') }}
                                </label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>

                            @if (!empty($captchaEnabled) && !empty($captchaSiteKey))
                                <input
                                    type="hidden"
                                    name="g-recaptcha-response"
                                    id="g-recaptcha-response"
                                    value=""
                                >

                                @error('g-recaptcha-response')
                                    <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="small text-muted mb-4">
                                    {!! __('users::users.public.recaptcha_notice_html', [
                                        'privacy_policy' => '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">' . e(__('users::users.public.privacy_policy')) . '</a>',
                                        'terms_of_service' => '<a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">' . e(__('users::users.public.terms_of_service')) . '</a>',
                                    ]) !!}
                                </div>
                            @endif

                            <div class="d-grid gap-2 auth-actions">
                                <button type="submit" class="btn btn-primary" id="register-submit-button">
                                    {{ __('users::users.public.register_submit') }}
                                </button>

                 <div class="text-center mt-3 small text-muted">
				 {{ __('core-localization::app.have_account') }}
                    <a href="{{ route('login') }}" class="text-decoration-none">
                         {{ __('core-localization::app.here') }}
                    </a>
                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (!empty($captchaEnabled) && !empty($captchaSiteKey))
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($captchaSiteKey) }}"></script>
        <script>
            (function () {
                window.addEventListener('load', function () {
                    const form = document.getElementById('register-form');
                    const submitButton = document.getElementById('register-submit-button');
                    const tokenInput = document.getElementById('g-recaptcha-response');
                    const siteKey = @json($captchaSiteKey);
                    const action = @json($captchaAction);
                    let isSubmitting = false;

                    if (!form || !submitButton || !tokenInput || !siteKey || !action) {
                        return;
                    }

                    const enableSubmit = function () {
                        submitButton.disabled = false;
                        submitButton.removeAttribute('aria-disabled');
                    };

                    const disableSubmit = function () {
                        submitButton.disabled = true;
                        submitButton.setAttribute('aria-disabled', 'true');
                    };

                    form.addEventListener('submit', function (event) {
                        if (isSubmitting) {
                            return;
                        }

                        event.preventDefault();

                        if (typeof window.grecaptcha === 'undefined') {
                            enableSubmit();
                            return;
                        }

                        disableSubmit();

                        window.grecaptcha.ready(function () {
                            window.grecaptcha.execute(siteKey, { action: action })
                                .then(function (token) {
                                    if (!token) {
                                        enableSubmit();
                                        return;
                                    }

                                    tokenInput.value = token;
                                    isSubmitting = true;
                                    form.submit();
                                })
                                .catch(function () {
                                    enableSubmit();
                                });
                        });
                    });
                });
            })();
        </script>
    @endif
@endpush