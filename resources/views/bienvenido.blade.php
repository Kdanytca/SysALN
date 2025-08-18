<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </x-slot>


    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Primera fila -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <!-- Card Instituciones -->
            <div class="bg-blue-600 text-white rounded-lg shadow p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Instituciones</h3>
                    <p class="text-5xl font-extrabold">{{ $institucionesCount }}</p>
                    <p class="mt-1 text-lg">Total de instituciones registradas</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('instituciones.index') }}"
                        class="inline-block bg-white text-blue-600 font-semibold px-4 py-2 rounded hover:bg-gray-100 transition">
                        Ver Instituciones
                    </a>
                </div>
            </div>

            <!-- Card Planes -->
            <div class="bg-green-600 text-white rounded-lg shadow p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Planes Estratégicos</h3>
                    <p class="text-5xl font-extrabold">{{ $planesCount }}</p>
                    <p class="mt-1 text-lg">Total de planes estratégicos registrados</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('planes.index') }}"
                        class="inline-block bg-white text-green-600 font-semibold px-4 py-2 rounded hover:bg-gray-100 transition">
                        Ver Planes
                    </a>
                </div>
            </div>

        </div>

        <!-- Segunda fila -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card Usuarios -->
            <div class="bg-yellow-500 text-white rounded-lg shadow p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Usuarios</h3>
                    <p class="text-5xl font-extrabold">{{ $usuariosCount ?? 0 }}</p>
                    <p class="mt-1 text-lg">Total de usuarios registrados</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('usuarios.index') }}"
                        class="inline-block bg-white text-yellow-600 font-semibold px-4 py-2 rounded hover:bg-gray-100 transition">
                        Ver Usuarios
                    </a>
                </div>
            </div>

            <!-- Otra Card -->
            <div class="bg-purple-600 text-white rounded-lg shadow p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Departamentos</h3>
                    <p class="text-5xl font-extrabold">{{ $departamentosCount ?? 0 }}</p>
                    <p class="mt-1 text-lg">Departamentos Registrados</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('departamentos.index_general') }}"
                        class="inline-block bg-white text-purple-600 font-semibold px-4 py-2 rounded hover:bg-gray-100 transition">
                        Ver Departamentos
                    </a>
                </div>
            </div>

        </div>

    </div>



</x-app-layout>
