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
                    <h1 class="text-2xl font-bold">Lista de Metas del Plan Estrategico: "{{ $plan->nombre_plan_estrategico }}"</h1>

                    <!-- Botón para agregar un nuevo registro -->
                    <div x-data="{ modalOpen: false }">
                        <button @click="modalOpen = true"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Agregar Nueva Meta
                        </button>

                        <!-- Modal -->
                        <div x-show="modalOpen"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                            <div @click.away="modalOpen = false"
                                class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                <h2 class="text-lg font-semibold mb-4">Registrar Nueva Meta</h2>
                                @include('metas.create')
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de metas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario Responsable</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre de la Meta</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ejes Estrategicos</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actividades</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Inicio</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Finalizacion</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Comentario</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($metas as $meta)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->usuario_responsable }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->nombre_meta }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->ejes_estrategicos }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->nombre_actividades}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->fecha_inicio }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->fecha_fin }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $meta->comentario }}</td>
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
                                                <h2 class="text-lg font-semibold mb-4">Editar Meta</h2>

                                                @include('metas.edit', [
                                                'action' => route('metas.update', $meta->id),
                                                'isEdit' => true,
                                                'meta' => $meta,
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
                                                    Meta?</p>

                                                <div class="flex justify-end space-x-3">
                                                    <button @click="confirmDelete = false"
                                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                        Cancelar
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('metas.destroy', $meta->id) }}">
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

                                    <a href="{{ route('meta.actividades', $meta->id) }}"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        Actividades
                                    </a>

                                </td>
                            </tr>
                            @endforeach

                            @if($metas->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay metas
                                    registradas.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</x-app-layout>