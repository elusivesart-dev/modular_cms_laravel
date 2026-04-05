<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $currentUser = auth()->user();
        $siteName = function_exists('settings')
            ? settings('general.site_name', config('app.name', 'MCMS'))
            : config('app.name', 'MCMS');

        $rbacResolver = app(\App\Core\RBAC\Contracts\RBACResolverInterface::class);
        $roleManager = app(\App\Core\RBAC\Contracts\RoleManagerInterface::class);
        $languageRegistry = app(\App\Core\Localization\Contracts\LanguageRegistryInterface::class);

        $isSuperAdmin = $currentUser
            && $roleManager->hasRoleForSubject(
                'super-admin',
                $currentUser::class,
                $currentUser->getAuthIdentifier()
            );

        $canViewSettings = $currentUser && $rbacResolver->can($currentUser, 'settings.view');
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

        $defaultAvatar = admin_theme_asset('images/avatar-4.jpg');
        $userAvatar = $currentUser?->avatar_url ?? $defaultAvatar;

        $userDisplayName = $currentUser?->name ?: $currentUser?->email ?: __('core-localization::web.user');
        $userSecondaryText = $currentUser?->email ?: $siteName;
    @endphp

    <title>
        {{ $siteName }}
        @hasSection('title')
            — @yield('title')
        @endif
    </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="icon" href="{{ admin_theme_asset('images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

    <link rel="stylesheet" href="{{ admin_theme_asset('pages/waves/css/waves.min.css') }}" type="text/css" media="all">
    <link rel="stylesheet" href="{{ admin_theme_asset('css/bootstrap/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ admin_theme_asset('icon/themify-icons/themify-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ admin_theme_asset('icon/font-awesome/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ admin_theme_asset('css/jquery.mCustomScrollbar.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ admin_theme_asset('css/style.css') }}" type="text/css">

    <style>
    .header-notification.dropdown .dropdown-menu.language-dropdown-menu {
        min-width: 180px;
        width: 180px;
        padding: 6px 0;
        border: none;
        border-radius: 6px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        max-height: 260px;
        overflow-y: auto;
    }

    .header-notification.dropdown .dropdown-menu.language-dropdown-menu li {
        list-style: none;
    }

    .header-notification.dropdown .dropdown-menu.language-dropdown-menu li a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        color: #666;
        text-decoration: none;
        background: #fff;
        font-size: 13px;
        line-height: 1.2;
    }

    .header-notification.dropdown .dropdown-menu.language-dropdown-menu li a:hover,
    .header-notification.dropdown .dropdown-menu.language-dropdown-menu li a.active {
        background: #f5f5f5;
        color: #000;
    }

    .language-flag-icon {
        width: 16px;
        height: 12px;
        object-fit: cover;
        display: inline-block;
        flex: 0 0 16px;
        border-radius: 2px;
        vertical-align: middle;
        margin-right: 0;
    }

    .admin-user-avatar {
        object-fit: cover;
        background: #f5f5f5;
    }

    .navbar-user-name {
        max-width: 180px;
        display: inline-block;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar-user-email {
        display: block;
        font-size: 12px;
        color: #666;
        margin-top: 4px;
        word-break: break-word;
    }
</style>

    @stack('styles')
</head>
<body>
<div class="theme-loader">
    <div class="loader-track">
        <div class="preloader-wrapper">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left"><div class="circle"></div></div>
                <div class="gap-patch"><div class="circle"></div></div>
                <div class="circle-clipper right"><div class="circle"></div></div>
            </div>
            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left"><div class="circle"></div></div>
                <div class="gap-patch"><div class="circle"></div></div>
                <div class="circle-clipper right"><div class="circle"></div></div>
            </div>
            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left"><div class="circle"></div></div>
                <div class="gap-patch"><div class="circle"></div></div>
                <div class="circle-clipper right"><div class="circle"></div></div>
            </div>
            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left"><div class="circle"></div></div>
                <div class="gap-patch"><div class="circle"></div></div>
                <div class="circle-clipper right"><div class="circle"></div></div>
            </div>
        </div>
    </div>
</div>

<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>

    <div class="pcoded-container navbar-wrapper">
        <nav class="navbar header-navbar pcoded-header">
            <div class="navbar-wrapper">
                <div class="navbar-logo">
                    <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                        <i class="ti-menu"></i>
                    </a>

                    <div class="mobile-search waves-effect waves-light">
                        <div class="header-search">
                            <div class="main-search morphsearch-search">
                                <div class="input-group">
                                    <span class="input-group-prepend search-close">
                                        <i class="ti-close input-group-text"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="{{ __('core-localization::web.search') }}">
                                    <span class="input-group-append search-btn">
                                        <i class="ti-search input-group-text"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/') }}" class="d-flex align-items-center">
                        <span class="img-fluid text-white font-weight-bold">{{ $siteName }}</span>
                    </a>

                    <a class="mobile-options waves-effect waves-light">
                        <i class="ti-more"></i>
                    </a>
                </div>

                <div class="navbar-container container-fluid">
                    <ul class="nav-left">
                        <li>
                            <div class="sidebar_toggle">
                                <a href="javascript:void(0)">
                                    <i class="ti-menu"></i>
                                </a>
                            </div>
                        </li>
                        <li>
                            <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                <i class="ti-fullscreen"></i>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav-right">
                        <li class="header-notification dropdown">
                            <a
                                href="#"
                                class="dropdown-toggle waves-effect waves-light"
                                id="adminLanguageDropdown"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <img src="{{ $currentLocaleFlag }}" alt="{{ $currentLocale }}" class="language-flag-icon">
                                <span>{{ $currentLocaleLabel }}</span>
                                
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right language-dropdown-menu" aria-labelledby="adminLanguageDropdown">
                                @foreach($availableLocales as $localeCode => $localeLabel)
                                    @php
                                        $localeFlag = $resolveFlagAsset($localeCode);
                                    @endphp
                                    <li>
                                        <a
                                            class="{{ $currentLocale === $localeCode ? 'active' : '' }}"
                                            href="{{ route('locale.switch', ['locale' => $localeCode]) }}"
                                        >
                                            <img src="{{ $localeFlag }}" alt="{{ $localeCode }}" class="language-flag-icon">
                                            <span>{{ $localeLabel }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        <li class="header-notification">
                            <a href="{{ url('/') }}" class="waves-effect waves-light">
                                <i class="ti-world"></i>
                                <span>{{ __('core-localization::web.site') }}</span>
                            </a>
                        </li>

                        @auth
                            <li class="user-profile header-notification">
                                <a href="#!" class="waves-effect waves-light">
                                    <img
                                        src="{{ $userAvatar }}"
                                        class="img-radius admin-user-avatar"
                                        alt="{{ $userDisplayName }}"
                                        style="width: 40px; height: 40px;"
                                    >
                                    <span class="navbar-user-name">{{ $userDisplayName }}</span>
                                    <i class="ti-angle-down"></i>
                                </a>

                                <ul class="show-notification profile-notification">
                                    <li class="waves-effect waves-light">
                                        <a href="{{ route('profile.me') }}">
                                            <i class="ti-user"></i>
                                            {{ __('core-localization::web.profile') }}
                                        </a>
                                    </li>

                                    <li class="waves-effect waves-light">
                                        <a href="{{ url('/') }}">
                                            <i class="ti-home"></i>
                                            {{ __('core-localization::web.site') }}
                                        </a>
                                    </li>

                                    @if($canViewSettings)
                                        <li class="waves-effect waves-light">
                                            <a href="{{ route('settings.index') }}">
                                                <i class="ti-settings"></i>
                                                {{ __('core-localization::web.settings') }}
                                            </a>
                                        </li>
                                    @endif

                                    <li class="waves-effect waves-light">
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-left w-100">
                                                <i class="ti-layout-sidebar-left"></i>
                                                {{ __('core-localization::web.logout') }}
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

        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                <nav class="pcoded-navbar">
                    <div class="sidebar_toggle">
                        <a href="#">
                            <i class="icon-close icons"></i>
                        </a>
                    </div>

                    <div class="pcoded-inner-navbar main-menu">
                        <div class="">
                            <div class="main-menu-header">
                                <img
                                    class="img-80 img-radius admin-user-avatar"
                                    src="{{ $userAvatar }}"
                                    alt="{{ $userDisplayName }}"
                                    style="width: 80px; height: 80px;"
                                >
                                <div class="user-details">
                                    <span id="more-details">
                                        {{ $userDisplayName }}
                                        <i class="fa fa-caret-down"></i>
                                    </span>
                                    <span class="sidebar-user-email">{{ $userSecondaryText }}</span>
                                </div>
                            </div>

                            <div class="main-menu-content">
                                <ul>
                                    <li class="more-details">
                                        <a href="{{ route('profile.me') }}">
                                            <i class="ti-user"></i>
                                            {{ __('core-localization::web.profile') }}
                                        </a>

                                        <a href="{{ url('/') }}">
                                            <i class="ti-home"></i>
                                            {{ __('core-localization::web.view_site') }}
                                        </a>

                                        @if($canViewSettings)
                                            <a href="{{ route('settings.index') }}">
                                                <i class="ti-settings"></i>
                                                {{ __('core-localization::web.settings') }}
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-left w-100">
                                                <i class="ti-layout-sidebar-left"></i>
                                                {{ __('core-localization::web.logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="p-15 p-b-0">
                            <form class="form-material">
                                <div class="form-group form-primary">
                                    <input type="text" name="sidebar-search" class="form-control">
                                    <span class="form-bar"></span>
                                    <label class="float-label">
                                        <i class="fa fa-search m-r-10"></i>
                                        {{ __('core-localization::web.search') }}
                                    </label>
                                </div>
                            </form>
                        </div>

                        <div class="pcoded-navigation-label">
                            {{ __('core-localization::web.navigation') }}
                        </div>

                        <ul class="pcoded-item pcoded-left-item">
                            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <a href="{{ route('dashboard') }}" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                                    <span class="pcoded-mtext">{{ __('core-localization::web.dashboard') }}</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                            </li>

                            @can('viewAny', \Modules\Users\Infrastructure\Models\User::class)
                                <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <a href="{{ route('users.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-user"></i><b>U</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.users') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            @endcan

                            @can('viewAny', \Modules\Roles\Infrastructure\Models\Role::class)
                                <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <a href="{{ route('roles.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-shield"></i><b>R</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.roles') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            @endcan

                            @if($canViewSettings)
                                <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('settings.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-settings"></i><b>S</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.settings') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            @endif
                        </ul>

                        @if($isSuperAdmin)
                            <div class="pcoded-navigation-label">
                                {{ __('core-localization::web.system') }}
                            </div>

                            <ul class="pcoded-item pcoded-left-item">
                                <li class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                    <a href="{{ route('permissions.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-key"></i><b>P</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.permissions') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('themes.*') ? 'active' : '' }}">
                                    <a href="{{ route('themes.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-palette"></i><b>T</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.themes') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('audit.*') ? 'active' : '' }}">
                                    <a href="{{ route('audit.index') }}" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-agenda"></i><b>A</b></span>
                                        <span class="pcoded-mtext">{{ __('core-localization::web.audit_log') }}</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </nav>

                <div class="pcoded-content">
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10">
                                            @yield('title', __('core-localization::web.admin_panel'))
                                        </h5>
                                        <p class="text-info">{{ $siteName }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('dashboard') }}">
                                                <i class="fa fa-home"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="#!">@yield('title', __('core-localization::web.admin_panel'))</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pcoded-inner-content">
                        <div class="main-body">
                            <div class="page-wrapper">
                                <div class="page-body">
                                    @yield('content')
                                </div>
                            </div>

                            <div id="styleSelector"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ admin_theme_asset('js/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/popper.js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ admin_theme_asset('pages/waves/js/waves.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
<script src="{{ admin_theme_asset('js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script src="{{ admin_theme_asset('js/pcoded.min.js') }}"></script>
<script src="{{ admin_theme_asset('js/vertical/vertical-layout.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/script.js') }}"></script>

@stack('scripts')
</body>
</html>