<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MCMS') }}</title>
</head>
<body>
    <form id="logout-form" method="POST" action="{{ route('logout') }}">
        @csrf
    </form>

    <script>
        document.getElementById('logout-form').submit();
    </script>
</body>
</html>