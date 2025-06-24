<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Planes Estratégicos
            </h2>
            <a href="#" @click.prevent="alert('Funcionalidad para crear nuevo plan no implementada aún')"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow border border-green-700 hover:bg-green-700 focus:outline-none cursor-pointer">
                ➕ Nuevo Plan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            modalOpen: false,
            metaActual: null,
            metas: {},
            planes: @json($planes)
        }">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <table class="min-w-full table-auto border border-gray-300">
                    <thead class="bg-gray-100 text-gray-700 text-sm">
                        <tr>
                            <th class="px-4 py-2 border">Nombre</th>
                            <th class="px-4 py-2 border">Metas</th>
                            <th class="px-4 py-2 border">Ejes Estratégicos</th>
                            <th class="px-4 py-2 border">Inicio</th>
                            <th class="px-4 py-2 border">Fin</th>
                            <th class="px-4 py-2 border">Indicador</th>
                            <th class="px-4 py-2 border">Responsable</th>
                            <th class="px-4 py-2 border">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-sm">
                        @foreach ($planes as $index => $plan)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2">{{ $plan['nombre_plan_estrategico'] }}</td>
                                <td class="border px-3 py-2">
                                    <button @click="modalOpen = true; metaActual = {{ $index }};"
                                        class="px-3 py-1 bg-blue-600 text-white rounded border border-blue-700 hover:bg-blue-700 focus:outline-none cursor-pointer">
                                        Ver / Agregar Metas
                                    </button>
                                </td>
                                <td class="border px-3 py-2">{{ $plan['ejes_estrategicos'] }}</td>
                                <td class="border px-3 py-2">{{ $plan['fecha_inicio'] }}</td>
                                <td class="border px-3 py-2">{{ $plan['fecha_fin'] }}</td>
                                <td class="border px-3 py-2">{{ $plan['indicador'] }}</td>
                                <td class="border px-3 py-2">{{ $plan['responsable'] }}</td>
                                <td class="border px-3 py-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="#" @click.prevent="alert('Función Ver no implementada aún')"
                                            class="text-blue-600 hover:underline cursor-pointer">Ver</a>
                                        <a href="#" @click.prevent="alert('Función Editar no implementada aún')"
                                            class="text-yellow-500 hover:underline cursor-pointer">Editar</a>
                                        <a href="#" @click.prevent="alert('Función Eliminar no implementada aún')"
                                            class="text-red-600 hover:underline cursor-pointer">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Modal para metas (fuera de tbody) --}}
                <div x-show="modalOpen"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                    style="display: none;" @click.self="modalOpen = false" @keydown.escape.window="modalOpen = false">
                    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Metas del plan: <span
                                x-text="metaActual !== null ? planes[metaActual].nombre_plan_estrategico : ''"></span>
                        </h3>

                        <template x-if="metaActual !== null">
                            <div>
                                <!-- Lista de metas actuales -->
                                <template x-if="metas[metaActual] && metas[metaActual].length > 0">
                                    <ul class="mb-4 list-disc list-inside text-left">
                                        <template x-for="(meta, idx) in metas[metaActual]" :key="idx">
                                            <li x-text="meta"></li>
                                        </template>
                                    </ul>
                                </template>

                                <!-- Input para nueva meta -->
                                <input type="text" x-model="nuevaMeta" placeholder="Escribe una meta"
                                    class="w-full border border-gray-300 rounded px-3 py-2 mb-3 focus:outline-none focus:ring focus:ring-blue-300" />
                                <button
                                    @click="
                        if(nuevaMeta && nuevaMeta.trim() !== '') {
                            if(!metas[metaActual]) metas[metaActual] = [];
                            metas[metaActual].push(nuevaMeta.trim());
                            nuevaMeta = '';
                        } else {
                            alert('Escribe una meta antes de agregar');
                        }
                    "
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none cursor-pointer">
                                    Agregar Meta
                                </button>

                                <div class="mt-4">
                                    <button
                                        @click="alert('Aquí agregarías actividades para la meta: ' + (metas[metaActual] ? metas[metaActual][metas[metaActual].length-1] : ''))"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 focus:outline-none cursor-pointer">
                                        Agregar Actividades
                                    </button>
                                </div>

                                <div class="mt-6 text-right">
                                    <button @click="modalOpen = false"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 focus:outline-none cursor-pointer">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>


                <div class="mt-6 text-sm text-gray-600 italic">
                    * Esta es una vista simulada. En el futuro podrás ver, editar y eliminar los planes reales desde
                    aquí.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
