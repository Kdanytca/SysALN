<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Lista de Metas / Estrategias del Plan Estrategico:
                "{{ $plan->nombre_plan_estrategico }}"</h2>

            <!-- Botón para agregar un nuevo registro -->
            <div x-data="{ modalOpen: false, modalNuevoUsuario: false }">
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

                <!-- Modal -->
                <div x-show="modalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4">Registrar Nueva Meta / Estrategia</h2>
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
                            'origen' => 'metas',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">

                <!-- Tabla de metas -->
                <table
                    class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                Usuario Responsable</th>
                            <th class="px-4 py-3 text-left">
                                Nombre de la Meta</th>
                            <th class="px-4 py-3 text-left break-words max-w-xs">
                                Objetivos Estrategicos</th>
                            <th class="px-4 py-3 text-left max-w-[150px]">
                                Ejes Estrategicos</th>
                            <th class="px-4 py-3 text-left max-w-[150px]">
                                Actividades</th>
                            <th class="px-4 py-3 text-left break-words max-w-xs">
                                Resultados</th>
                            <th class="px-4 py-3 text-left break-words max-w-xs">
                                Indicador</th>
                            <th class="px-4 py-3 text-left">
                                Fechas</th>
                            <th class="px-4 py-3 text-left break-words max-w-xs">
                                Comentario</th>
                            <th class="px-4 py-3 text-left">
                                Estado</th>
                            <th class="px-4 py-3 text-center">Funciones del Sistema</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($metas as $meta)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium break-words max-w-xs">
                                    {{ $meta->encargadoMeta->nombre_usuario ?? 'Sin asignar' }}</td>
                                <td class="px-4 py-3 break-words max-w-xs">
                                    {{ $meta->nombre }}</td>
                                <td class="px-4 py-3 break-words max-w-xs">
                                    @php
                                        $objetivos = json_decode($meta->objetivos_estrategias);
                                        // Filtra los objetivos no nulos y no vacíos
                                        $objetivos_filtrados = collect($objetivos)->filter(function($item) {
                                            return !is_null($item) && $item !== '';
                                        });
                                    @endphp

                                    @if ($objetivos_filtrados->isNotEmpty())
                                        @foreach ($objetivos_filtrados as $obj)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                                {{ $obj }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin objetivos</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-[150px] break-words">
                                    @if (!empty($meta->ejes_estrategicos))
                                        @foreach (json_decode($meta->ejes_estrategicos, true) ?? [] as $eje)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                                {{ $eje }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin ejes seleccionados</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-[150px] break-words">
                                    @if (!empty($meta->nombre_actividades))
                                        @foreach (json_decode($meta->nombre_actividades, true) as $actividad)
                                            <div class="text-gray-800 text-xs mb-1">
                                                • {{ trim($actividad) }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin actividades registradas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    {{ $meta->resultados_esperados ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    {{ $meta->indicador_resultados }}
                                </td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    Inicio:<br>
                                    <div class="font-semibold text-indigo-600">
                                        {{ \Carbon\Carbon::parse($meta->fecha_inicio)->format('d-m-Y') }}
                                    </div>
                                    <br>
                                    Fin:<br>
                                    <div class="font-semibold text-indigo-600">
                                        {{ \Carbon\Carbon::parse($meta->fecha_fin)->format('d-m-Y') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    {{ $meta->comentario ?? 'N/A' }}</td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    @php
                                        $inicio = \Carbon\Carbon::parse($meta->fecha_inicio);
                                        $fin = \Carbon\Carbon::parse($meta->fecha_fin);
                                        $hoy = \Carbon\Carbon::now();

                                        $color = 'bg-gray-400'; // Por defecto: gris

                                        if ($hoy->lt($inicio)) {
                                            $color = 'bg-gray-400'; // Aún no empieza
                                        } elseif ($hoy->between($inicio, $fin)) {
                                            $duracionTotal = $inicio->diffInSeconds($fin);
                                            $duracionTranscurrida = $inicio->diffInSeconds($hoy);
                                            $porcentaje = ($duracionTranscurrida / $duracionTotal) * 100;

                                            if ($porcentaje < 50) {
                                                $color = 'bg-green-500';
                                            } elseif ($porcentaje >= 50 && $porcentaje <= 100) {
                                                $color = 'bg-yellow-400';
                                            }
                                        } elseif ($hoy->gt($fin)) {
                                            $color = 'bg-red-500'; // Ya pasó el tiempo
                                        }
                                    @endphp

                                    <div class="flex justify-center">
                                        <div class="w-4 h-4 rounded-full {{ $color }}" title="Avance: {{ round($porcentaje ?? 0, 1) }}%"></div>
                                    </div>
                                </td>
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
                                                        <h2 class="text-lg font-semibold mb-4">Editar Meta / Estrategia</h2>
                                                        @include('metas.edit', [
                                                            'action' => route('metas.update', $meta->id),
                                                            'isEdit' => true,
                                                            'meta' => $meta,
                                                            'plan' => $meta->planEstrategico,
                                                            'usuarios' => $usuarios,
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
                                                            'ocultarCamposRelacionados' => false,
                                                            'institucion' => $institucion,
                                                            'instituciones' => $instituciones ?? collect(),
                                                            'departamentos' => $departamentos ?? collect(),
                                                            'vistaMetas' => $vistaMetas ?? true,
                                                            'origen' => 'metas',
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

                                        @if ($rol === 'encargado_institucion' || $rol === 'responsable_plan')
                                            <div class="border-b border-gray-300 w-16 mx-1"></div>
                                        @endif

                                        {{-- Actividades (lo ven todos) --}}
                                        <a href="{{ route('meta.actividades', $meta->id) }}"
                                            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-xs hover:bg-blue-200 transition shadow-sm">
                                            Actividades/Lineas de Acción
                                        </a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach

                        @if ($metas->isEmpty())
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500">No hay metas
                                    registradas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <br>
                @if (isset($plan))
                    @auth
                        @if (in_array(auth()->user()->tipo_usuario, ['administrador', 'responsable_plan', 'encargado_institucion']))
                            @php
                                switch (auth()->user()->tipo_usuario) {
                                    case 'responsable_plan':
                                        $rutaInicio = route('plan.responsable', $plan->id);
                                        break;
                                    case 'administrador':
                                        $rutaInicio = route('institucion.planes', $plan->departamento->institucion->id);
                                        break;
                                    case 'encargado_institucion':
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
            </div>
        </div>
    </div>

    <script>
        function agregarCampo(contenedorId, inputName) {
            const contenedor = document.getElementById(contenedorId);

            const wrapper = document.createElement('div');
            wrapper.className = 'input-con-x mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = inputName;
            input.className = 'border rounded px-3 py-2 w-full';
            input.required = true;

            const botonEliminar = document.createElement('button');
            botonEliminar.type = 'button';
            botonEliminar.innerText = '×';
            botonEliminar.onclick = function () {
                eliminarEsteCampo(botonEliminar);
            };

            wrapper.appendChild(input);
            wrapper.appendChild(botonEliminar);
            contenedor.appendChild(wrapper);
        }

        function eliminarEsteCampo(boton) {
            const wrapper = boton.parentElement;
            wrapper.remove();
        }

        function limpiarFormularioCrear() {
            const formulario = document.querySelector('[x-ref="formNuevaMeta"]');
            if (formulario) formulario.reset();

            const contenedor = document.getElementById('contenedorActividades');
            if (contenedor) {
                contenedor.innerHTML = '';
                const wrapper = document.createElement('div');
                wrapper.className = 'input-con-x mb-2';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'nombre_actividades[]';
                input.className = 'border rounded px-3 py-2 w-full';
                input.required = true;

                const botonEliminar = document.createElement('button');
                botonEliminar.type = 'button';
                botonEliminar.innerText = '×';
                botonEliminar.onclick = function () {
                    eliminarEsteCampo(botonEliminar);
                };

                wrapper.appendChild(input);
                wrapper.appendChild(botonEliminar);
                contenedor.appendChild(wrapper);
            }
        }

        // Validación de fechas al enviar el formulario
        document.addEventListener("submit", function (e) {
            const form = e.target;

            if (!form.classList.contains("formMeta")) return;

            const inicio = form.querySelector("[name='fecha_inicio']").value;
            const fin = form.querySelector("[name='fecha_fin']").value;

            // Fechas del plan desde los atributos data-*
            const planInicio = form.dataset.fechaInicioPlan;
            const planFin = form.dataset.fechaFinPlan;

            let errores = [];

            if (!inicio || !fin) return;

            // Validaciones
            if (new Date(inicio) > new Date(fin)) {
                errores.push("⚠️ La fecha de inicio no puede ser mayor que la fecha de fin.");
            }

            if (new Date(inicio) < new Date(planInicio)) {
                errores.push(`⚠️ La fecha de inicio no puede ser anterior al inicio del plan (${planInicio}).`);
            }

            if (new Date(fin) > new Date(planFin)) {
                errores.push(`⚠️ La fecha de fin no puede ser posterior al fin del plan (${planFin}).`);
            }

            if (errores.length > 0) {
                e.preventDefault();
                mostrarAlerta(errores, form);
            }
        });

        function mostrarAlerta(mensajes, form) {
            let alerta = form.querySelector("#alertaFechas");
            if (!alerta) {
                alerta = document.createElement("div");
                alerta.id = "alertaFechas";
                alerta.className = "bg-yellow-100 text-yellow-800 border border-yellow-400 px-4 py-2 rounded mb-4";
                form.insertBefore(alerta, form.firstChild);
            }

            alerta.innerHTML = mensajes.map(msg => `<div>${msg}</div>`).join('');
        }

    </script>


</x-app-layout>
