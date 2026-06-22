<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf-token() }}">

    <title>{{ config('app.name', 'POS System') }}</title>

    <!-- Scripts y Estilos de tu proyecto -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden antialiased">

    <!-- Aquí se renderizará tu componente POS a pantalla completa -->
    <main class="h-full w-full">
        {{ $slot }}
    </main>

</body>
</html>
