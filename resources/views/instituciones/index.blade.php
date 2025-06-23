<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Lista de Instituciones</h1>

                    <!-- Botón para agregar un nuevo registro -->
                    <div x-data="{ modalOpen: false }">
                        <button @click="modalOpen = true"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Agregar Nueva Institución
                        </button>

                        <!-- Modal -->
                        <div x-show="modalOpen"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                            <div @click.away="modalOpen = false"
                                class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                <h2 class="text-lg font-semibold mb-4">Registrar Nueva Institución</h2>
                                @include('instituciones.create')
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de instituciones -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Encargado</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($instituciones as $institucion)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $institucion->nombre_institucion }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $institucion->tipo_institucion }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $institucion->encargado_proyecto }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div x-data="{ editModalOpen: false }" class="inline-block">
                                        <button @click="editModalOpen = true"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Editar
                                        </button>

                                        <!-- Modal de edición -->
                                        <div x-show="editModalOpen"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                            x-cloak>
                                            <div @click.away="editModalOpen = false"
                                                class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                                <h2 class="text-lg font-semibold mb-4">Editar Institución</h2>

                                                @include('instituciones.edit', [
                                                'action' => route('instituciones.update', $institucion->id),
                                                'isEdit' => true,
                                                'institucion' => $institucion,
                                                ])
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Eliminar -->
                                    <div x-data="{ confirmDelete: false }" class="inline-block">
                                        <!-- Botón que abre el modal -->
                                        <button @click="confirmDelete = true" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>

                                        <!-- Modal de confirmación -->
                                        <div x-show="confirmDelete"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                            x-cloak>
                                            <div @click.away="confirmDelete = false"
                                                class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                                                <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmar
                                                    eliminación</h2>
                                                <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar esta
                                                    institución?</p>

                                                <div class="flex justify-end space-x-3">
                                                    <button @click="confirmDelete = false"
                                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                        Cancelar
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('instituciones.destroy', $institucion->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('institucion.departamentos', $institucion->id) }}"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        Ver Departamentos
                                    </a>

                                </td>
                            </tr>
                            @endforeach

                            @if($instituciones->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay instituciones
                                    registradas.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</x-app-layout>