<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            @if (isset($meta))
                <h1>Lista de Actividades de la Meta: "{{ $meta->nombre_meta }}"</h1>
            @else
                <h1>Lista de Tus Actividades</h1>
            @endif

            <div class="flex items-center space-x-4">
                <!-- Botón para agregar nueva actividad (abre modal) -->
                <div x-data="{ modalOpen: false, modalNuevoUsuario: false }">
                    @php
                        $rol = Auth::user()->tipo_usuario ?? null;
                    @endphp

                    @if (in_array($rol, ['encargado_institucion', 'responsable_plan', 'responsable_meta']))
                        <button @click="modalOpen = true"
                            class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva Actividad
                        </button>
                    @endif

                    <!-- Modal -->
                    <div x-show="modalOpen"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                            <h2 class="text-xl font-bold mb-4">Registrar Nueva Actividad</h2>
                            @include('actividades.create', [
                                'departamentos' => $departamentos,
                                'metas' => $metas,
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
                                'origen' => 'actividades',
                            ])
                        </div>
                    </div>
                </div>

                @if (isset($meta))
                    <a href="{{ route('meta.resumen_seguimientos', $meta->id) }}"
                        class="inline-flex items-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 shadow-sm transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        Ver resumen general de seguimientos
                    </a>
                @endif
            </div>
        </div>
    </x-slot>



    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <!-- Tabla de actividades -->
                @php
                    $rol = Auth::user()->tipo_usuario ?? null;
                    $rolesPermitidos = ['encargado_institucion', 'responsable_plan', 'responsable_meta'];
                @endphp

                <table
                    class="min-w-full border border-gray-300 rounded-lg shadow text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="w-1/9 px-4 py-3 text-left">Usuario</th>
                            <th class="w-1/9 px-4 py-3 text-left max-w-xs break-words">Nombre de la Actividad</th>
                            <th class="w-1/9 px-4 py-3 text-left max-w-[200px] break-words">Objetivos</th>
                            <th class="w-1/9 px-4 py-3 text-left whitespace-nowrap">Inicio</th>
                            <th class="w-1/9 px-4 py-3 text-left whitespace-nowrap">Fin</th>
                            <th class="w-1/9 px-4 py-3 text-left max-w-xs break-words">Comentario</th>
                            <th class="w-1/9 px-4 py-3 text-left max-w-xs break-words">Unidad Encargada</th>
                            <th class="w-1/8 px-4 py-3 text-left">Estado</th>

                            {{-- Solo mostrar columna Acciones a roles permitidos --}}
                            @if (in_array($rol, $rolesPermitidos))
                                <th class="px-4 py-3 text-center whitespace-nowrap">Funciones<br> del Sistema</th>
                            @endif

                            <th class="px-4 py-3 text-center whitespace-nowrap">Seguimiento</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($actividades as $actividad)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium break-words max-w-xs">
                                    {{ $actividad->encargadoActividad->nombre_usuario ?? 'Sin asignar' }}</td>
                                <td class="px-4 py-3 max-w-xs break-words">
                                    {{ $actividad->nombre_actividad }}</td>
                                <td class="px-4 py-3 max-w-[200px] break-words">
                                    @if (!empty($actividad->objetivos))
                                        @foreach (json_decode($actividad->objetivos, true) as $objetivo)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                                {{ trim($objetivo) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-red-500">Sin objetivos registrados</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($actividad->fecha_fin)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 max-w-xs break-words whitespace-normal">{{ $actividad->comentario }}</td>
                                <td class="px-4 py-3 max-w-xs break-words whitespace-normal">{{ $actividad->unidad_encargada ?? 'Sin asignar' }}</td>
                                <td class="px-4 py-3 break-words max-w-xs whitespace-normal">
                                    @php
                                        $inicio = \Carbon\Carbon::parse($actividad->fecha_inicio);
                                        $fin = \Carbon\Carbon::parse($actividad->fecha_fin);
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

                                {{-- Acciones solo para roles permitidos --}}
                                @if (in_array($rol, $rolesPermitidos))
                                    <td class="px-4 py-3 text-left max-w-xs break-words whitespace-normal">
                                        <div class="flex flex-col items-center space-y-2">
                                            {{-- Editar --}}
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
                                                        <h2 class="text-lg font-semibold mb-4">Editar Actividad</h2>
                                                        @include('actividades.edit', [
                                                            'action' => route(
                                                                'actividades.update',
                                                                $actividad->id),
                                                            'isEdit' => true,
                                                            'actividad' => $actividad,
                                                            'departamentos' => $departamentos,
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
                                                            'origen' => 'actividades',
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

                                                <!-- Modal de confirmación -->
                                                <div x-show="confirmDelete"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                                                    x-cloak>
                                                    <div
                                                        class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                                                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmar
                                                            eliminación</h2>
                                                        <p class="text-gray-600 mb-6">¿Estás seguro de que deseas
                                                            eliminar esta
                                                            Actividad?</p>

                                                        <div class="flex justify-end items-center gap-3 items-stretch">
                                                            <div>
                                                                <button @click="confirmDelete = false"
                                                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                                    Cancelar
                                                                </button>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <form method="POST"
                                                                    action="{{ route('actividades.destroy', $actividad->id) }}">
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
                                        </div>
                                    </td>
                                @endif

                                {{-- Seguimiento visible para todos --}}
                                <td class="text-center px-4 py-2 whitespace-nowrap">
                                    <div class="flex flex-col items-center space-y-2">
                                        <button onclick="abrirModalCrearSeguimiento({{ $actividad->id }})"
                                            class="bg-purple-300 text-purple-800 px-3 py-1 rounded hover:bg-purple-400 transition text-sm">
                                            Seguimiento
                                        </button>
                                        <button onclick="mostrarSeguimientos({{ $actividad->id }})"
                                            class="bg-blue-300 text-blue-800 px-3 py-1 rounded hover:bg-blue-400 transition text-sm">
                                            Ver Seguimientos
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($actividades->isEmpty())
                            <tr>
                                <td colspan="{{ in_array($rol, $rolesPermitidos) ? 10 : 9 }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay actividades registradas.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <br>
                @auth
                    @if (in_array(auth()->user()->tipo_usuario, ['administrador', 'responsable_meta', 'encargado_institucion', 'responsable_plan']))
                        @php
                            switch (auth()->user()->tipo_usuario) {
                                case 'responsable_meta':
                                    $rutaInicio = route('meta.responsable');
                                    break;
                                case 'administrador':
                                    $rutaInicio = route('plan.metas', $meta->planEstrategico->id);
                                    break;
                                case 'encargado_institucion':
                                    $rutaInicio = route('plan.metas', $meta->planEstrategico->id);
                                    break;
                                case 'responsable_plan':
                                    $rutaInicio = route('plan.metas', $meta->planEstrategico->id);
                                    break;
                                default:
                                    $rutaInicio = '#';
                            }
                        @endphp

                        @if ($rutaInicio !== '#' && isset($meta) && $meta->planEstrategico)
                            <div class="mb-6">
                                <div
                                    class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200">
                                    <a href="{{ $rutaInicio }}" class="flex items-center space-x-1 text-sm font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        <span>Volver a metas</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif
                @endauth

            </div>
        </div>
    </div>

    <!-- Modal para agregar / editar Seguimientos -->
    <div id="modalSeguimiento"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-70 hidden">
        <div class="bg-white p-6 rounded shadow-md w-full max-w-md relative">
            <button onclick="cerrarModalSeguimiento()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500">✕</button>

            <h2 id="tituloModalSeguimiento" class="text-lg font-bold mb-4">Agregar Seguimiento</h2>

            <form method="POST" id="formSeguimiento" onsubmit="guardarSeguimiento(event)">
                @csrf
                <input type="hidden" name="id" id="seguimiento_id_modal" value="">
                <input type="hidden" name="idActividades" id="actividad_id_modal" value="">

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Periodo</label>
                    <input type="date" name="periodo_consultar" id="periodo_consultar_modal" required
                        class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Estado</label>
                    <select name="estado" id="estado_modal" required class="w-full border rounded px-3 py-2">
                        <option value="">Seleccione</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en progreso">En progreso</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones_modal" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="text-right">
                    <button type="submit" id="botonGuardarSeguimiento"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Guardar Seguimiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal que muestra los Seguimientos -->
    <div id="modalVerSeguimientos"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-2xl p-6 rounded shadow-lg relative max-h-[90vh] overflow-y-auto">
            <button onclick="cerrarModalVerSeguimientos()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500">✕</button>

            <h2 class="text-lg font-bold mb-4">Seguimientos de la Actividad</h2>

            <div id="contenidoSeguimientos">
                <p class="text-gray-600">Cargando...</p>
            </div>
        </div>
    </div>


    <script>
        let modoEdicion = false;
        let ultimoActividadIdAbierta = null;

        // Mostrar modal ver seguimientos
        function mostrarSeguimientos(actividadId) {
            ultimoActividadIdAbierta = actividadId;

            document.getElementById('modalVerSeguimientos').classList.remove('hidden');

            const contenido = document.getElementById('contenidoSeguimientos');
            contenido.innerHTML = '<p class="text-gray-600">Cargando...</p>';

            fetch(`/actividades/${actividadId}/seguimientos`)
                .then(response => response.text())
                .then(html => {
                    contenido.innerHTML = html;
                })
                .catch(() => {
                    contenido.innerHTML = '<p class="text-red-600">Error al cargar los seguimientos.</p>';
                });
        }

        // Cerrar modal ver seguimientos
        function cerrarModalVerSeguimientos() {
            document.getElementById('modalVerSeguimientos').classList.add('hidden');
        }

        // Abrir modal crear seguimiento
        function abrirModalCrearSeguimiento(idActividad) {
            modoEdicion = false;

            document.querySelector('#modalSeguimiento h2').textContent = 'Agregar Seguimiento';
            document.querySelector('#modalSeguimiento form').reset();
            document.querySelector('#modalSeguimiento form').action = "{{ route('seguimientos.store') }}";

            let methodInput = document.querySelector('#modalSeguimiento input[name="_method"]');
            if (methodInput) methodInput.remove();

            document.getElementById('actividad_id_modal').value = idActividad;

            document.getElementById('modalSeguimiento').classList.remove('hidden');
        }

        // Abrir modal editar seguimiento
        function abrirModalEditarSeguimiento(seguimiento) {
            modoEdicion = true;

            document.querySelector('#modalSeguimiento h2').textContent = 'Editar Seguimiento';
            document.querySelector('#modalSeguimiento form').action = `/seguimientos/${seguimiento.id}`;

            let methodInput = document.querySelector('#modalSeguimiento input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                document.querySelector('#modalSeguimiento form').appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            document.getElementById('actividad_id_modal').value = seguimiento.idActividades || '';
            document.querySelector('input[name="periodo_consultar"]').value = seguimiento.periodo_consultar || '';
            document.querySelector('select[name="estado"]').value = seguimiento.estado || '';
            document.querySelector('textarea[name="observaciones"]').value = seguimiento.observaciones || '';

            // Forzar z-index para que edición quede arriba
            const modalEdicion = document.getElementById('modalSeguimiento');
            const modalVer = document.getElementById('modalVerSeguimientos');
            modalEdicion.style.zIndex = '9999';
            modalVer.style.zIndex = '9998';

            modalEdicion.classList.remove('hidden');
        }


        // Cerrar modal edición
        function cerrarModalSeguimiento() {
            const modalEdicion = document.getElementById('modalSeguimiento');
            const modalVer = document.getElementById('modalVerSeguimientos');

            modalEdicion.classList.add('hidden');

            // Restaurar z-index original
            modalEdicion.style.zIndex = '';
            modalVer.style.zIndex = '';

            if (modoEdicion && ultimoActividadIdAbierta) {
                recargarTablaSeguimientos();
            }

            document.querySelector('#modalSeguimiento form').reset();
            let methodInput = document.querySelector('#modalSeguimiento input[name="_method"]');
            if (methodInput) methodInput.remove();
        }


        // Recargar tabla dentro del modal ver seguimientos
        function recargarTablaSeguimientos() {
            if (!ultimoActividadIdAbierta) return;

            fetch(`/actividades/${ultimoActividadIdAbierta}/seguimientos`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('contenidoSeguimientos').innerHTML = html;
                });
        }

        // Eliminar seguimiento con confirmación y recarga
        function eliminarSeguimiento(id) {
            if (!confirm("¿Deseas eliminar este seguimiento?")) return;

            fetch(`/seguimientos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('No se pudo eliminar');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        recargarTablaSeguimientos();
                    } else {
                        alert('Ocurrió un error al eliminar.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Error al intentar eliminar.');
                });
        }
        async function guardarSeguimiento(event) {
            event.preventDefault();

            const boton = document.getElementById('botonGuardarSeguimiento');
            boton.disabled = true;
            boton.textContent = 'Guardando...';

            const form = event.target;
            const action = form.action;
            const formData = new FormData(form);

            // Si el formulario es para editar, aseguramos que _method esté en formData
            if (formData.get('_method') === 'PUT') {
                // Ya está puesto, perfecto
            } else {
                // Para creación o si no está definido, eliminar _method si existe
                formData.delete('_method');
            }

            try {
                const response = await fetch(action, {
                    method: 'POST', // Siempre POST para Laravel, con _method dentro de formData
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'No se pudo guardar'));
                    return;
                }

                cerrarModalSeguimiento();

                if (ultimoActividadIdAbierta) {
                    recargarTablaSeguimientos();
                }
            } catch (error) {
                console.error(error);
                alert('Error al guardar seguimiento.');
            } finally {
                boton.disabled = false;
                boton.textContent = 'Guardar Seguimiento';
            }
        }

        // Funciones para agregar/eliminar campos dinámicos (si se usan en el formulario)
        function agregarCampo(contenedorId, name) {
            const contenedor = document.getElementById(contenedorId);
            
            const wrapper = document.createElement('div');
            wrapper.className = 'input-con-x mb-2';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.name = name;
            input.required = true;
            input.className = 'border rounded px-3 py-2 w-full';

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

        // Funcion para validacion de fechas
        document.addEventListener("submit", function (e) {
            const form = e.target;

            if (!form.classList.contains("formActividad")) return;

            const inicio = form.querySelector("[name='fecha_inicio']").value;
            const fin = form.querySelector("[name='fecha_fin']").value;

            // Fechas de la meta desde los atributos data-*
            const metaInicio = form.dataset.fechaInicioMeta;
            const metaFin = form.dataset.fechaFinMeta;

            let errores = [];

            if (!inicio || !fin) return;

            // Validaciones
            if (new Date(inicio) > new Date(fin)) {
                errores.push("⚠️ La fecha de inicio no puede ser mayor que la fecha de fin.");
            }

            if (new Date(inicio) < new Date(metaInicio)) {
                errores.push(`⚠️ La fecha de inicio no puede ser anterior al inicio de la meta (${metaInicio}).`);
            }

            if (new Date(fin) > new Date(metaFin)) {
                errores.push(`⚠️ La fecha de fin no puede ser posterior al fin de la meta (${metaFin}).`);
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

        // Limpiar formulario al cerrar modal
        function limpiarFormularioCrearActividad() {
            const formulario = document.querySelector('[x-ref="formNuevaActividad"]');
            if (formulario) formulario.reset();

            // Limpiar contenedor de objetivos
            const contenedorObjetivos = document.getElementById('contenedorObjetivos');
            if (contenedorObjetivos) {
                contenedorObjetivos.innerHTML = '';

                const wrapper = document.createElement('div');
                wrapper.className = 'input-con-x mb-2';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'objetivos[]';
                input.className = 'border rounded px-3 py-2';
                input.required = true;

                const botonEliminar = document.createElement('button');
                botonEliminar.type = 'button';
                botonEliminar.innerText = '×';
                botonEliminar.onclick = function () {
                    eliminarEsteCampo(botonEliminar);
                };

                wrapper.appendChild(input);
                wrapper.appendChild(botonEliminar);
                contenedorObjetivos.appendChild(wrapper);
            }

            // Restaurar select de unidad encargada a su valor por defecto
            const unidadDisplay = document.getElementById('unidad_encargada_display_nuevo');
            const unidadHidden = document.getElementById('unidad_encargada_nuevo');
            if (unidadDisplay && unidadHidden) {
                unidadDisplay.selectedIndex = 0; // Seleccionar "Sin Departamento"
                unidadHidden.value = '';
            }

            // Reiniciar model Alpine si aplica (usuario seleccionado, unidad, imágenes, etc.)
            if (typeof Alpine !== 'undefined') {
                const alpineComponent = formulario.__x;
                if (alpineComponent && alpineComponent.$data) {
                    const data = alpineComponent.$data;

                    // Campos de datos generales
                    if ('usuarioSeleccionado' in data) data.usuarioSeleccionado = '';
                    if ('unidad' in data) data.unidad = '';

                    // 🔹 LIMPIAR PREVISUALIZACIÓN DE IMÁGENES
                    if ('nuevasImagenes' in data) data.nuevasImagenes = [];
                    if ('modalVisible' in data) data.modalVisible = false;
                    if ('imagenActual' in data) data.imagenActual = '';

                    // 🔹 Limpiar el input file
                    const inputFile = formulario.querySelector('input[type="file"][name="imagenes[]"]');
                    if (inputFile) inputFile.value = '';
                }
            }
        }

        // Alpine.js para previsualización de imágenes
        function previsualizacionImagenes(config = {}) {
            return {
                nuevasImagenes: [],
                imagenesExistentes: config.existentes || [],
                imagenesEliminadas: [],
                modalVisible: false,
                imagenActual: '',

                init() {
                    this.nuevasImagenes = [];
                    this.imagenesExistentes = this.imagenesExistentes || [];
                },

                // Manejar archivos seleccionados
                manejarArchivos(event) {
                    const archivos = Array.from(event.target.files);
                    for (let archivo of archivos) {
                        if (!archivo.type.startsWith('image/')) continue;
                        const previewUrl = URL.createObjectURL(archivo);
                        this.nuevasImagenes.push({ file: archivo, preview: previewUrl });
                    }

                    // Reconstruir el input para mantener todos los archivos
                    this.actualizarInput();
                },

                eliminarNueva(index) {
                    if (index >= 0 && index < this.nuevasImagenes.length) {
                        URL.revokeObjectURL(this.nuevasImagenes[index].preview);
                        this.nuevasImagenes.splice(index, 1);
                    }

                    // Reconstruir input
                    this.actualizarInput();
                },

                eliminarExistente(index) {
                    const imagen = this.imagenesExistentes[index];
                    if (imagen) {
                        if (!this.imagenesEliminadas.includes(imagen)) {
                            this.imagenesEliminadas.push(imagen);
                        }
                        this.imagenesExistentes.splice(index, 1);

                        // Inputs ocultos para eliminar
                        this.$nextTick(() => {
                            let contenedor = document.getElementById('inputs-eliminar');
                            if (contenedor) {
                                contenedor.innerHTML = '';
                                this.imagenesEliminadas.forEach(img => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'eliminar_imagenes[]';
                                    input.value = img;
                                    contenedor.appendChild(input);
                                });
                            }
                        });
                    }
                },

                actualizarInput() {
                    if (this.$refs.inputImagenes) {
                        const dt = new DataTransfer();
                        this.nuevasImagenes.forEach(img => dt.items.add(img.file));
                        this.$refs.inputImagenes.files = dt.files;
                    }
                },

                verImagen(url) {
                    this.imagenActual = url;
                    this.modalVisible = true;
                }
            };
        }
    </script>
</x-app-layout>
