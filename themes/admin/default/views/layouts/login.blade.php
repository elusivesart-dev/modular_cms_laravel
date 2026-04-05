<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteName = function_exists('settings')
            ? settings('general.site_name', config('app.name', 'MCMS'))
            : config('app.name', 'MCMS');
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

    <link rel="stylesheet" type="text/css" href="{{ admin_theme_asset('css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ admin_theme_asset('pages/waves/css/waves.min.css') }}" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="{{ admin_theme_asset('icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ admin_theme_asset('icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ admin_theme_asset('icon/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ admin_theme_asset('css/style.css') }}">

    @stack('styles')
</head>

<body themebg-pattern="theme1">
<div class="theme-loader">
    <div class="loader-track">
        <div class="preloader-wrapper">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="login-block">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                @yield('content')
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="{{ admin_theme_asset('js/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/popper.js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ admin_theme_asset('pages/waves/js/waves.min.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
<script type="text/javascript" src="{{ admin_theme_asset('js/common-pages.js') }}"></script>

@stack('scripts')
</body>
</html>