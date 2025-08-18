<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<input type="hidden" name="origen" value="instituciones_show">
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Lista de Instituciones</h2>

            @if (auth()->user()->tipo_usuario === 'administrador')
            <!-- Botón solo visible para administradores -->
            <div x-data="{ modalInstitucion: false, modalNuevoUsuario: false }">
                <button @click="modalInstitucion = true"
                    class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Institución
                </button>

                <!-- Modal de Institución -->
                <div x-show="modalInstitucion"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4">Registrar Nueva Institución</h2>
                        @include('instituciones.create', [
                            'usuarios' => $usuariosParaCrear,
                            'closeModal' => 'modalInstitucion = false'
                        ])
                    </div>
                </div>

                <!-- Modal de Usuario -->
                <div x-show="modalNuevoUsuario"
                    x-on:close-modal-usuario.window="modalNuevoUsuario = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4">Registrar Nuevo Usuario</h2>

                        @include('instituciones.usuario', [
                            'closeModal' => 'modalNuevoUsuario = false',
                            'ocultarCamposRelacionados' => true
                        ])
                    </div>
                </div>
            </div>
            @endif
        </div>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- Tabla de instituciones -->
                <table
                    class="w-full table-fixed border border-gray-300 rounded-lg overflow-hidden shadow text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="w-1/5 px-4 py-3 text-left">
                                Nombre</th>
                            <th class="w-1/5 px-4 py-3 text-left">
                                Tipo</th>
                            <th class="w-1/5 px-4 py-3 text-left">
                                Encargado</th>
                            <th class="w-2/5 px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($instituciones as $institucion)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium truncate">
                                    {{ $institucion->nombre_institucion ?? '-' }}</td>
                                <td class="px-4 py-3 truncate">
                                    {{ $institucion->tipo_institucion ?? '-' }}</td>
                                <td class="px-4 py-3 truncate">
                                    {{ $institucion->encargadoInstitucion->nombre_usuario ?? '-'}}</td>
                                <td class="px-4 py-3 text-righ">
                                    <div class="flex flex-wrap justify-center gap-2">
                                        {{-- Editar --}}
                                        <div x-data="{ editModalOpen: false, modalNuevoUsuario: false }">
                                            <button @click="editModalOpen = true"
                                                class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-md text-xs hover:bg-yellow-200 transition shadow-sm">
                                                Editar
                                            </button>

                                            <div x-show="editModalOpen"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                x-cloak>
                                                <div
                                                    class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                    <h2 class="text-xl font-bold mb-4">Editar Institución</h2>
                                                    @include('instituciones.edit', [
                                                        'action' => route(
                                                            'instituciones.update',
                                                            $institucion->id),
                                                        'isEdit' => true,
                                                        'institucion' => $institucion,
                                                        'usuariosParaEditar' => $usuariosParaEditar,
                                                    ])
                                                </div>
                                            </div>

                                            <!-- Modal de Usuario dentro de editar -->
                                            <div x-show="modalNuevoUsuario"
                                                x-on:close-modal-usuario.window="modalNuevoUsuario = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                                                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                    <h2 class="text-xl font-bold mb-4">Registrar Nuevo Usuario</h2>

                                                    @include('instituciones.usuario', [
                                                        'closeModal' => 'modalNuevoUsuario = false',
                                                        'ocultarCamposRelacionados' => true
                                                    ])
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Eliminar --}}
                                        <div x-data="{ confirmDelete: false }" class="inline-block">

                                            <button @click="confirmDelete = true"
                                                class="bg-red-100 text-red-800 px-3 py-1.5 rounded-md text-xs hover:bg-red-200 transition shadow-sm">
                                                Eliminar
                                            </button>

                                            <div x-show="confirmDelete"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                x-cloak>
                                                <div
                                                    class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmar
                                                        eliminación</h2>
                                                    <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar
                                                        esta
                                                        institución?</p>

                                                    <div class="flex justify-end items-center gap-3 items-stretch">
                                                        <div>
                                                            <button @click="confirmDelete = false"
                                                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                                Cancelar
                                                            </button>
                                                        </div>

                                                        <div class="flex items-center">
                                                            <form method="POST"
                                                                action="{{ route('instituciones.destroy', $institucion->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 align-middle">
                                                                    Eliminar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Ver Departamentos --}}
                                        <a href="{{ route('institucion.departamentos', $institucion->id) }}"
                                            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-xs hover:bg-blue-200 transition shadow-sm">
                                            Ver Departamentos
                                        </a>

                                        {{-- Ver Planes Estratégicos --}}
                                        <a href="{{ route('institucion.planes', $institucion->id) }}"
                                            class="bg-green-100 text-green-800 px-3 py-1.5 rounded-md text-xs hover:bg-green-200 transition shadow-sm">
                                            Ver Planes Estratégicos
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                        @if ($instituciones->isEmpty())
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">No hay
                                    instituciones registradas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <br>
                @auth
                    @if (auth()->user()->tipo_usuario === 'administrador')
                        <div class="mb-6">
                            <div
                                class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200">
                                <a href="{{ route('dashboard') }}" class="flex items-center space-x-1 text-sm font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span>Volver al inicio</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
