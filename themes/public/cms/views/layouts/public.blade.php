<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $currentUser = auth()->user();

        $siteName = function_exists('settings')
            ? settings('general.site_name', config('app.name', 'MCMS'))
            : config('app.name', 'MCMS');

        $siteDescription = function_exists('settings')
            ? settings('general.site_description', 'Modular CMS Website')
            : 'Modular CMS Website';

        $themeAsset = static function (string $path): string {
            if (function_exists('theme_asset')) {
                return theme_asset('public', 'cms', $path);
            }

            if (function_exists('public_theme_asset')) {
                return public_theme_asset($path);
            }

            return asset('themes/public/cms/' . ltrim($path, '/'));
        };

        $navigationItems = $navigationItems ?? [
            ['title' => __('core-localization::app.home'), 'url' => url('/')],
            ['title' => __('core-localization::app.blog'), 'url' => url('/blog')],
            ['title' => __('core-localization::app.posts'), 'url' => url('/posts')],
            ['title' => __('core-localization::app.contacts'), 'url' => url('/contact')],
			['title' => __('core-localization::app.aboutus'), 'url' => url('/aboutus')],
        ];
    @endphp

    <title>@yield('title', $siteName)</title>
    <meta name="description" content="@yield('meta_description', $siteDescription)">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ $themeAsset('assets/css/public.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="public-theme-body">
    @include('public-theme::partials.navbar', [
        'siteName' => $siteName,
        'navigationItems' => $navigationItems,
        'currentUser' => $currentUser,
    ])

    <main class="public-main">
        @yield('hero')

        <section class="py-5">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </main>

    @include('public-theme::partials.footer', [
        'siteName' => $siteName,
    ])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ $themeAsset('assets/js/public.js') }}"></script>
    @stack('scripts')
</body>
</html>