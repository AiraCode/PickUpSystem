<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'One Stop Solution | Modern Mulia Mandiri' }}</title>
        <meta name="description" content="One Stop Solution Modern Mulia Mandiri untuk penjualan aki reject.">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/user-api.js'])
    </head>
    <body class="user-shell {{ $bodyClass ?? '' }}">
        @yield('content')
    </body>
</html>
