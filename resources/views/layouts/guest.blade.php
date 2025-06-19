<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex">

    {{-- Información lado izquierdo (oculto en móvil) --}}
    <div
        class="hidden md:flex flex-1 bg-gradient-to-br from-green-900 to-amber-700 text-white flex-col justify-center p-16">
        <h1 class="text-4xl font-bold mb-6">Bienvenido a Sistema de Planeación Estratégica</h1>
        <div
            class="flex-0 flex items-center justify-center bg-gradient-to-br from-blue-800 to-purple-700 overflow-hidden rounded-2xl">
            <img src="{{ asset('img/login1.jpg') }}" alt="Ilustración login"
                class="h-[400px] w-auto object-cover shadow-2xl" />
        </div>


    </div>

    {{-- Formulario lado derecho --}}
    <div class="flex flex-col flex-1 justify-center items-center bg-gray-50 p-8">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-10">
            <div class="flex justify-center mb-2">
                <img src="{{ asset('img/ALN.png') }}" alt="Logo" class="h-32 w-32 rounded-full object-cover" />
            </div>



            {{ $slot }}

        </div>
    </div>

</body>

</html>
