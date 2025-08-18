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

<body class="flex flex-col min-h-screen font-sans antialiased bg-gray-100">
    <!-- NAVBAR -->
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                {{-- Logo a la izquierda (redirecciona diferente según el tipo de usuario) --}}
                <div class="flex-shrink-0">
                    @php
                        use Illuminate\Support\Facades\Auth;

                        $user = Auth::user();
                        $rol = $user->tipo_usuario ?? null;

                        // Definir la ruta de inicio según el rol
                        switch ($rol) {
                            case 'administrador':
                                $rutaInicio = route('dashboard');
                                break;

                            case 'encargado_institucion':
                                $rutaInicio = route('institucion.ver', $user->idInstitucion);
                                break;

                            case 'encargado_departamento':
                                $rutaInicio = url("/departamento/{$user->idDepartamento}");
                                break;

                            case 'responsable_plan':
                                $plan = $user->planEstrategico ?? null;
                                $rutaInicio = $plan ? url("/plan-estrategico/{$plan->id}") : url('/sin-plan-asignado');
                                break;

                            case 'responsable_meta':
                                $rutaInicio = route('meta.responsable');
                                break;

                            case 'responsable_actividad':
                                $rutaInicio = route('actividades.indexResponsable');
                                break;

                            default:
                                $rutaInicio = '#';
                                break;
                        }
                    @endphp

                    <a href="{{ $rutaInicio }}" class="flex items-center space-x-2">
                        <i class="bi bi-house-door-fill text-xl"></i>
                        <span class="font-bold text-lg">SysALN</span>
                    </a>
                </div>
                
                {{-- Enlaces del menú (solo admin puede verlos) --}}
                <div class="hidden md:flex space-x-6 items-center">
                    @if ($rol === 'administrador')
                        <a href="{{ route('dashboard') }}"
                            class="hover:text-gray-300 {{ request()->routeIs('dashboard') ? 'underline' : '' }}">
                            <i class="bi bi-house-door"></i> Dashboard
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
                            <i class="bi bi-people"></i> Usuarios
                        </a>

                        <a href="{{ route('departamentos.index_general') }}"
                            class="hover:text-gray-300 {{ request()->routeIs('departamentos.*') ? 'underline' : '' }}">
                            <i class="bi bi-building"></i> Departamentos
                        </a>
                    @endif

                    {{-- Botón cerrar sesión para todos los roles --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-gray-300 flex items-center space-x-1">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- ALERTAS DE SESIÓN -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3 mx-3 fw-bold fs-5 shadow border border-success"
            role="alert" style="background-color: #d4edda;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3 mx-3 fw-bold fs-5 shadow border border-danger"
            role="alert" style="background-color: #f44336; color: white;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    <!-- ALERTAS DE SESIÓN -->


    <!-- HEADER (Si existe) -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-grow">
        {{ $slot }}
    </main>


    <footer class="bg-gray-800 text-gray-100 text-sm py-6 px-8">
        <div
            class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center space-y-6 md:space-y-0">

            <!-- Izquierda -->
            <div class="md:w-1/3 text-left flex flex-col items-start space-y-2">
                <a href="https://virtual3.itca.edu.sv/?redirect=0" class="inline-block bg-white p-1 rounded">
                    <img src="{{ asset('img/logoITCA.png') }}" alt="Logo ITCA"
                        class="h-12 w-auto filter brightness-110 contrast-125" />
                </a>

                <p class="leading-relaxed text-gray-300 text-sm">
                    En la Escuela Especializada en Ingeniería ITCA-FEPADE estamos comprometidos con la calidad
                    académica, la
                    empresarialidad y la pertinencia de nuestra oferta educativa.
                </p>
            </div>



            <!-- Centro -->
            <div class="md:w-1/3 text-center space-y-1">
                <a href="#" class="block hover:underline">Moodle community</a>
                <a href="#" class="block hover:underline">Moodle free support</a>
                <a href="#" class="block hover:underline">Moodle Docs</a>
                <a href="#" class="block hover:underline">Moodle.com</a>
            </div>

            <!-- Derecha -->
            <div class="md:w-1/3 text-right space-y-2">
                <div class="flex justify-end space-x-4 mb-2">
                    <!-- Íconos minimalistas -->
                    <a href="https://www.facebook.com/profile.php?id=100064701425306" target="_blank"
                        aria-label="Facebook" class="text-gray-300 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22.675 0h-21.35c-.733 0-1.325.592-1.325 1.325v21.351c0 .732.592 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.894-4.788 4.66-4.788 1.325 0 2.466.099 2.797.143v3.243l-1.918.001c-1.505 0-1.797.715-1.797 1.763v2.311h3.59l-.467 3.622h-3.123V24h6.116c.73 0 1.322-.592 1.322-1.324V1.325c0-.733-.592-1.325-1.325-1.325z" />
                        </svg>
                    </a>
                    <a href="https://x.com/itcafepade" target="_blank" aria-label="Twitter"
                        class="text-gray-300 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.867 9.867 0 01-3.127 1.195 4.917 4.917 0 00-8.38 4.482A13.957 13.957 0 011.671 3.15a4.917 4.917 0 001.523 6.555 4.897 4.897 0 01-2.228-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.085 4.918 4.918 0 004.588 3.417A9.867 9.867 0 010 19.54a13.94 13.94 0 007.548 2.209c9.142 0 14.307-7.721 13.995-14.646A9.936 9.936 0 0024 4.557z" />
                        </svg>
                    </a>
                </div>
                <div class="text-gray-400 leading-relaxed">
                    <p>CARRETERA A SANTA TECLA KM. 11, LA LIBERTAD, EL SALVADOR C.A</p>
                    <p>TEL. (503) 2132-7400. FAX (503) 2132-7406</p>
                </div>
            </div>

        </div>
    </footer>


</body>



</html>
