<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Lista de Metas del Plan Estrategico:
                "{{ $plan->nombre_plan_estrategico }}"</h2>

            <!-- Botón para agregar un nuevo registro -->
<<<<<<< HEAD
            <div x-data="{ modalOpen: false, modalNuevoUsuario: false }">
                <button @click="modalOpen = true"
                    class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Meta
                </button>
=======
            <div x-data="{ modalOpen: false }">
                @php
                    $rol = Auth::user()->tipo_usuario ?? null;
                @endphp

                @if ($rol === 'encargado_institucion' || $rol === 'responsable_plan')
                    <button @click="modalOpen = true"
                        class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Meta
                    </button>
                @endif

>>>>>>> 425ea6e4908ef69eb34aeecd8931abdcfd45bf79

                <!-- Modal -->
                <div x-show="modalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4">Registrar Nueva Meta</h2>
                        @include('metas.create')
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
                            'ocultarCamposRelacionados' => false,
                            'institucion' => $institucion,
                            'instituciones' => $instituciones ?? collect(),
                            'departamentos' => $departamentos ?? collect(),
                            'vistaMetas' => $vistaMetas ?? true,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- Tabla de metas -->
                <table
                    class="w-full table-fixed border border-gray-300 rounded-lg overflow-hidden shadow text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Usuario Responsable</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Nombre de la Meta</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Ejes Estrategicos</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Actividades</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Inicio</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Fin</th>
                            <th class="w-1/8 px-4 py-3 text-left">
                                Comentario</th>
                            <th class="w-1/8 px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($metas as $meta)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium">
                                    {{ $meta->encargadoMeta->nombre_usuario ?? 'Sin asignar' }}</td>
                                <td class="px-4 py-3">
                                    {{ $meta->nombre_meta }}</td>
                                <td class="px-4 py-3 max-w-[200px]">
                                    @if (!empty($meta->ejes_estrategicos))
                                        @foreach (explode(',', $meta->ejes_estrategicos) as $eje)
                                            <span
                                                class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                                {{ trim($eje) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin ejes seleccionados</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-[200px]">
                                    @if (!empty($meta->nombre_actividades))
                                        @foreach (explode(',', $meta->nombre_actividades) as $actividad)
                                            <span
                                                class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                                {{ trim($actividad) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin actividades registradas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($meta->fecha_inicio)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($meta->fecha_fin)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 max-w-xs truncate whitespace-normal break-words">
                                    {{ $meta->comentario }}</td>
                                <td class="px-4 py-3 text-righ">
                                    @php
                                        $rol = Auth::user()->tipo_usuario ?? null;
                                    @endphp

                                    <div class="flex flex-wrap justify-center gap-2">

                                        {{-- Editar (solo encargado_institucion o responsable_plan) --}}
                                        @if ($rol === 'encargado_institucion' || $rol === 'responsable_plan')
                                            <div x-data="{ editModalOpen: false, modalNuevoUsuario: false }" class="inline-block">
                                                <button @click="editModalOpen = true"
                                                    class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-md text-xs hover:bg-yellow-200 transition shadow-sm">
                                                    Editar
                                                </button>

                                                <!-- Modal de edición -->
                                                <div x-show="editModalOpen"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                    x-cloak>
                                                    <div
                                                        class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                        <h2 class="text-lg font-semibold mb-4">Editar Meta</h2>
                                                        @include('metas.edit', [
                                                            'action' => route('metas.update', $meta->id),
                                                            'isEdit' => true,
                                                            'meta' => $meta,
                                                            'plan' => $meta->planEstrategico,
                                                            'usuarios' => $usuarios,
                                                        ])
                                                    </div>
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
                                                        'ocultarCamposRelacionados' => false,
                                                        'institucion' => $institucion,
                                                        'instituciones' => $instituciones ?? collect(),
                                                        'departamentos' => $departamentos ?? collect(),
                                                        'vistaMetas' => $vistaMetas ?? true,
                                                    ])
                                                </div>
                                            </div>
                                        </div>

                                            <!-- Eliminar -->
                                            <div x-data="{ confirmDelete: false }" class="inline-block">
                                                <!-- Botón que abre el modal -->
                                                <button @click="confirmDelete = true"
                                                    class="bg-red-100 text-red-800 px-3 py-1.5 rounded-md text-xs hover:bg-red-200 transition shadow-sm">
                                                    Eliminar
                                                </button>

                                                <!-- Modal de confirmación -->
                                                <div x-show="confirmDelete"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                    x-cloak>
                                                    <div
                                                        class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmar
                                                            eliminación</h2>
                                                        <p class="text-gray-600 mb-6">¿Estás seguro de que deseas
                                                            eliminar
                                                            esta Meta?</p>

                                                        <div class="flex justify-end items-center gap-3 items-stretch">
                                                            <div>
                                                                <button @click="confirmDelete = false"
                                                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                                    Cancelar
                                                                </button>
                                                            </div>

                                                            <div class="flex items-center">
                                                                <form method="POST"
                                                                    action="{{ route('metas.destroy', $meta->id) }}">
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
                                        @endif

                                        {{-- Actividades (lo ven todos) --}}
                                        <a href="{{ route('meta.actividades', $meta->id) }}"
                                            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-xs hover:bg-blue-200 transition shadow-sm">
                                            Actividades
                                        </a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach

                        @if ($metas->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No hay metas
                                    registradas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <br>
                @if (isset($plan))
                    @auth
                        @if (in_array(auth()->user()->tipo_usuario, ['administrador', 'responsable_plan']))
                            @php
                                switch (auth()->user()->tipo_usuario) {
                                    case 'responsable_plan':
                                        $rutaInicio = route('plan.responsable', $plan->id);
                                        break;
                                    case 'administrador':
                                        $rutaInicio = route('institucion.planes', $plan->departamento->institucion->id);
                                        break;
                                    default:
                                        $rutaInicio = '#';
                                }
                            @endphp

                            @if ($rutaInicio !== '#')
                                <div class="mb-6">
                                    <div
                                        class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200">
                                        <a href="{{ $rutaInicio }}"
                                            class="flex items-center space-x-1 text-sm font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            <span>Volver a planes estratégicos</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endauth

                @endif


                <script>
                    function agregarActividad(contenedorId, botonEliminarId) {
                        const contenedor = document.getElementById(contenedorId);
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = 'nombre_actividades[]';
                        input.className = 'w-full border rounded px-3 py-2 mb-2';
                        input.required = true;
                        contenedor.appendChild(input);

                        document.getElementById(botonEliminarId).classList.remove('hidden');
                    }

                    function eliminarUltimaActividad(contenedorId, botonEliminarId) {
                        const contenedor = document.getElementById(contenedorId);
                        const inputs = contenedor.querySelectorAll('input');
                        if (inputs.length > 1) {
                            contenedor.removeChild(inputs[inputs.length - 1]);
                        }
                        if (inputs.length <= 2) {
                            document.getElementById(botonEliminarId).classList.add('hidden');
                        }
                    }

                    function limpiarFormularioCrear() {
                        const formulario = document.querySelector('[x-ref="formNuevaMeta"]');
                        if (formulario) formulario.reset();

                        const contenedor = document.getElementById('contenedorActividades');
                        if (contenedor) {
                            contenedor.innerHTML = ''; // Elimina todo
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.name = 'nombre_actividades[]';
                            input.className = 'w-full border rounded px-3 py-2 mb-2';
                            input.required = true;
                            contenedor.appendChild(input);
                        }

                        const btnEliminar = document.getElementById('btnEliminarActividad');
                        if (btnEliminar) btnEliminar.classList.add('hidden');
                    }
                </script>

</x-app-layout>
