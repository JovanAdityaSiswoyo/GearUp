<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AplikasiPinjam') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layouts.navbar')
    <main class="container mx-auto px-4">
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
