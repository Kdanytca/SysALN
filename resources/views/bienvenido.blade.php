<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-10 rounded shadow text-center">
            <h1 class="text-2xl font-bold mb-4 text-gray-700">¡Bienvenido!</h1>
            <p class="mb-6 text-gray-600">Has iniciado sesión correctamente.</p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
