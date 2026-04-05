@php
    use App\Core\Localization\Contracts\LanguageRegistryInterface;
    use App\Core\RBAC\Contracts\RBACResolverInterface;

    $currentUrl = url()->current();

    $userAvatar = null;
    $canAccessAdminPanel = false;

    $languageRegistry = app(LanguageRegistryInterface::class);
    $availableLocales = $languageRegistry->getDropdownOptions();
    $currentLocale = app()->getLocale();

    $resolveFlagAsset = static function (string $locale): string {
        $normalized = strtolower(trim($locale));
        $relativePath = 'assets/flags/' . $normalized . '.svg';

        return is_file(public_path($relativePath))
            ? asset($relativePath)
            : asset('assets/flags/default.svg');
    };

    $currentLocaleLabel = $availableLocales[$currentLocale] ?? strtoupper($currentLocale);
    $currentLocaleFlag = $resolveFlagAsset($currentLocale);

    if ($currentUser) {
        $resolvedAvatarUrl = isset($currentUser->avatar_url) && is_string($currentUser->avatar_url)
            ? trim($currentUser->avatar_url)
            : '';

        $userAvatar = $resolvedAvatarUrl !== '' ? $resolvedAvatarUrl : null;

        $rbacResolver = app(RBACResolverInterface::class);

        $canAccessAdminPanel = $rbacResolver->hasAnyRole($currentUser, [
            'super-admin',
            'admin',
        ]);
    }

    $initials = $currentUser
        ? mb_strtoupper(mb_substr((string) ($currentUser->name ?? 'U'), 0, 1))
        : null;

    $profileViewUrl = \Illuminate\Support\Facades\Route::has('profile.me')
        ? route('profile.me')
        : url('/profile');

    $profileEditUrl = \Illuminate\Support\Facades\Route::has('profile.edit')
        ? route('profile.edit')
        : url('/profile/edit');

    $adminPanelUrl = \Illuminate\Support\Facades\Route::has('dashboard')
        ? route('dashboard')
        : url('/admin');
@endphp

<nav class="navbar navbar-expand-lg navbar-dark public-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            {{ $siteName }}
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#publicNavbar"
            aria-controls="publicNavbar"
            aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @foreach($navigationItems as $item)
                    @php
                        $isActive = rtrim($currentUrl, '/') === rtrim($item['url'], '/');
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item['url'] }}">
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

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

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="publicLanguageDropdown">
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

                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                            {{ __('core-localization::web.login') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-warning btn-sm">
                            {{ __('users::users.public.register') }}
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown">
                        <button
                            class="btn user-menu-btn dropdown-toggle d-flex align-items-center gap-2"
                            type="button"
                            id="publicUserDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            @if($userAvatar)
                                <img
                                    src="{{ $userAvatar }}"
                                    alt="{{ $currentUser->name }}"
                                    class="public-user-avatar"
                                >
                            @else
                                <span class="public-user-avatar public-user-avatar-fallback">
                                    {{ $initials }}
                                </span>
                            @endif

                            <span class="public-user-name">
                                {{ $currentUser->name }}
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="publicUserDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ $profileViewUrl }}">
                                    {{ __('users::users.public.my_profile') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $profileEditUrl }}">
                                    {{ __('users::users.public.edit_profile') }}
                                </a>
                            </li>

                            @if($canAccessAdminPanel)
                                <li>
                                    <a class="dropdown-item" href="{{ $adminPanelUrl }}">
                                        {{ __('users::users.public.admin_panel') }}
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        {{ __('users::users.public.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>