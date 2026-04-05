<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteName = function_exists('settings')
            ? settings('general.site_name', config('app.name', 'MCMS'))
            : config('app.name', 'MCMS');

        $currentUser = auth()->user();
        $roleManager = app(\App\Core\RBAC\Contracts\RoleManagerInterface::class);
        $languageRegistry = app(\App\Core\Localization\Contracts\LanguageRegistryInterface::class);

        $canAccessAdmin = $currentUser !== null
            && $roleManager->hasAnyRoleForSubject(
                ['super-admin', 'admin'],
                $currentUser::class,
                (int) $currentUser->getAuthIdentifier()
            );

        $availableLocales = $languageRegistry->getDropdownOptions();
        $currentLocale = app()->getLocale();

        $resolveFlagAsset = static function (string $locale): string {
            $normalized = strtolower(trim($locale));
            $relativePath = 'assets/flags/' . $normalized . '.svg';

            return is_file(public_path($relativePath))
                ? asset($relativePath)
                : asset('assets/flags/default.svg');
        };

        $resolveUserAvatar = static function ($user): string {
            if ($user === null) {
                return 'https://via.placeholder.com/40x40';
            }

            try {
                if (method_exists($user, 'getFirstMediaUrl')) {
                    $mediaUrl = $user->getFirstMediaUrl('avatar');

                    if (is_string($mediaUrl) && trim($mediaUrl) !== '') {
                        return $mediaUrl;
                    }

                    $mediaUrl = $user->getFirstMediaUrl('avatars');

                    if (is_string($mediaUrl) && trim($mediaUrl) !== '') {
                        return $mediaUrl;
                    }
                }

                $profilePhotoUrl = $user->profile_photo_url ?? null;
                if (is_string($profilePhotoUrl) && trim($profilePhotoUrl) !== '') {
                    return $profilePhotoUrl;
                }

                $avatarUrl = $user->avatar_url ?? null;
                if (is_string($avatarUrl) && trim($avatarUrl) !== '') {
                    return $avatarUrl;
                }

                if (method_exists($user, 'getAttribute')) {
                    $avatar = $user->getAttribute('avatar');

                    if (is_string($avatar) && trim($avatar) !== '' && filter_var($avatar, FILTER_VALIDATE_URL)) {
                        return $avatar;
                    }
                }
            } catch (\Throwable $e) {
            }

            return 'https://via.placeholder.com/40x40';
        };

        $currentLocaleLabel = $availableLocales[$currentLocale] ?? strtoupper($currentLocale);
        $currentLocaleFlag = $resolveFlagAsset($currentLocale);
        $userAvatar = $resolveUserAvatar($currentUser);

        $profileViewUrl = \Illuminate\Support\Facades\Route::has('profile.me')
            ? route('profile.me')
            : '#!';

        $profileEditUrl = \Illuminate\Support\Facades\Route::has('profile.edit')
            ? route('profile.edit')
            : '#!';

        $adminPanelUrl = \Illuminate\Support\Facades\Route::has('users.index')
            ? route('users.index')
            : url('/admin');
    @endphp

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $siteName }}
        @hasSection('title')
            — @yield('title')
        @endif
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/vapor/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">
    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand fw-semibold">
            {{ $siteName }}
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#publicNavbar"
            aria-controls="publicNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle d-flex align-items-center"
                        href="#"
                        id="publicLanguageDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img
                            src="{{ $currentLocaleFlag }}"
                            alt="{{ $currentLocale }}"
                            width="18"
                            height="14"
                            class="me-2 rounded"
                        >
                        <span>{{ $currentLocaleLabel }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="publicLanguageDropdown">
                        @foreach($availableLocales as $localeCode => $localeLabel)
                            @php
                                $localeFlag = $resolveFlagAsset($localeCode);
                            @endphp
                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center {{ $currentLocale === $localeCode ? 'active' : '' }}"
                                    href="{{ route('locale.switch', ['locale' => $localeCode]) }}"
                                >
                                    <img
                                        src="{{ $localeFlag }}"
                                        alt="{{ $localeCode }}"
                                        width="18"
                                        height="14"
                                        class="me-2 rounded"
                                    >
                                    <span>{{ $localeLabel }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle d-flex align-items-center"
                            href="#"
                            id="publicUserDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img
                                src="{{ $userAvatar }}"
                                alt="{{ $currentUser->name ?? $currentUser->email ?? 'User' }}"
                                width="40"
                                height="40"
                                class="rounded-circle me-2"
                            >
                            <span>{{ $currentUser->name ?? $currentUser->email }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="publicUserDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ $profileViewUrl }}">
                                    <i class="bi bi-person me-2"></i>
                                    Виж профила
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $profileEditUrl }}">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Редактирай профила
                                </a>
                            </li>

                            @if($canAccessAdmin)
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>
                                        Админ панел
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Изход
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-secondary btn-sm">
                            {{ __('users::users.public.register') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-info btn-sm">
                            {{ __('core-localization::web.login') }}
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
<header class="position-relative overflow-hidden">
    <div
        class="card border-light mb-3 w-100 d-flex align-items-center justify-content-center text-center"
        style="
            min-height: 300px;
            background-image: url('{{ asset('images/header.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        "
    >
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75"></div>

        <div class="position-relative text-white px-3">
            <h1 class="display-4 fw-bold mb-3">
                {{ $siteName }}
            </h1>

            <p class="lead mb-4">
                @yield('header_subtitle', 'Добре дошъл в системата')
            </p>

            @guest
                <a href="{{ route('register') }}" class="btn btn-lg btn-primary me-2">
                    {{ __('users::users.public.register') }}
                </a>

                <a href="{{ route('login') }}" class="btn btn-lg btn-outline-light">
                    {{ __('core-localization::web.login') }}
                </a>
            @endguest
        </div>
    </div>
</header>
<main class="py-4">
    <div class="container">
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>