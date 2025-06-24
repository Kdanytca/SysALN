<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons CDN (para los íconos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Scripts (Tailwind, etc.) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <!-- NAVBAR -->
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <i class="bi bi-house-door-fill text-xl"></i>
                        <span class="font-bold text-lg">SysALN</span>
                    </a>
                </div>

                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}"
                        class="hover:text-gray-300 {{ request()->routeIs('dashboard') ? 'underline' : '' }}">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                    <a href="{{ route('instituciones.index') }}"
                        class="hover:text-gray-300 {{ request()->routeIs('instituciones.*') ? 'underline' : '' }}">
                        <i class="bi bi-building"></i> Instituciones
                    </a>
                    <a href="{{ route('planes.index') }}"
                        class="hover:text-gray-300 {{ request()->routeIs('planes.*') ? 'underline' : '' }}">
                        <i class="bi bi-kanban-fill"></i> Planes
                    </a>
                    <a href="{{ route('usuarios.index') }}"
                        class="hover:text-gray-300 {{ request()->routeIs('usuarios.*') ? 'underline' : '' }}">
                        <i class="bi bi-people"></i> Usiarios
                    </a>
                    <a href="{{ route('departamentos.index') }}"
                        class="hover:text-gray-300 {{ request()->routeIs('departamentos.*') ? 'underline' : '' }}">
                        <i class="bi bi-building"></i> Departamentos
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <!-- HEADER (Si existe) -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- CONTENIDO PRINCIPAL -->
    <main>
        {{ $slot }}
    </main>
</body>

</html>
