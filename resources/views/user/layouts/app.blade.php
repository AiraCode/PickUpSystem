<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Pick Up System | Indoprima Group' }}</title>
        <meta name="description" content="Pick Up System Indoprima Group untuk penjualan aki bekas.">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/user-api.js'])
    </head>
    <body class="user-shell {{ $bodyClass ?? '' }}">
        @yield('content')
    </body>
</html>
