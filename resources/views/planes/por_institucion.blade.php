<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Planes Estratégicos de: {{ $institucion->nombre_institucion }}
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('planes.backupIndex') }}"
                    class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-md hover:bg-blue-200 shadow-sm transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m7-7v14" />
                    </svg>
                    Ver Respaldos
                </a>

                <button id="btnNuevoPlan"
                    class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Plan
                </button>
            </div>
        </div>
    </x-slot>


    <br>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre Plan</th>
                            <th class="px-4 py-3 text-left">Departamento</th>
                            <th class="px-4 py-3 text-left">Responsable</th>
                            <th class="px-4 py-3 text-left">Ejes Estratégicos</th>
                            <th class="px-4 py-3 text-left">Objetivos</th>
                            <th class="px-4 py-3 text-left">Fecha Inicio</th>
                            <th class="px-4 py-3 text-left">Fecha Fin</th>
                            <th class="px-4 py-3 text-center">Indicador</th>
                            <th class="px-4 py-3 text-center">Funciones del Sistema</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($planes as $plan)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium break-words">{{ $plan->nombre_plan_estrategico }}</td>
                                <td class="px-4 py-3 break-words">{{ $plan->departamento->departamento ?? 'N/A' }}</td>
                                <td class="px-4 py-3 break-words">
                                    {{ $plan->responsable->nombre_usuario ?? 'Sin responsable' }}</td>
                                <!-- COLUMNA EJES ESTRATÉGICOS -->
                                <td class="px-4 py-3 max-w-xs">
                                    <div class="relative">
                                        @php
                                            $ejes = [];
                                            if ($plan->ejes_estrategicos) {
                                                $decodificado = json_decode($plan->ejes_estrategicos, true);
                                                $ejes = is_array($decodificado) ? array_filter($decodificado) : [];
                                            }
                                        @endphp

                                        <div class="overflow-hidden text-ellipsis max-h-20 whitespace-pre-line text-xs">
                                            @if (!empty($ejes))
                                                {{ implode("\n", array_map(fn($eje, $i) => $i + 1 . '. ' . str_replace("\n", ' ', $eje), $ejes, array_keys($ejes))) }}
                                            @else
                                                <span class="italic text-gray-400">Sin ejes estratégicos</span>
                                            @endif
                                        </div>

                                        @if (!empty($ejes))
                                            <button
                                                onclick="abrirModalTexto('Ejes - {{ $plan->nombre_plan_estrategico }}', {{ json_encode($ejes) }})"
                                                class="text-blue-600 text-xs mt-1 hover:underline">
                                                📋 Ver completo ({{ count($ejes) }})
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <!-- COLUMNA OBJETIVOS -->
                                <td class="px-4 py-3 max-w-xs">
                                    <div class="relative">
                                        @php
                                            $objetivos = [];
                                            if ($plan->objetivos) {
                                                $objetivos = json_decode($plan->objetivos, true);
                                                $objetivos = array_filter($objetivos); // Limpiar valores vacíos
                                            }
                                        @endphp

                                        <div class="overflow-hidden text-ellipsis max-h-20 whitespace-pre-line">
                                            @if (!empty($objetivos))
                                                {{ '• ' . implode("\n• ", $objetivos) }}
                                            @else
                                                <span class="italic text-gray-400">Sin objetivos</span>
                                            @endif
                                        </div>

                                        @if (!empty($objetivos))
                                            <button
                                                onclick="abrirModalTexto('Objetivos - {{ $plan->nombre_plan_estrategico }}', {{ json_encode($objetivos) }})"
                                                class="text-blue-600 text-xs mt-1 hover:underline">
                                                📋 Ver completo ({{ count($objetivos) }})
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 break-words">
                                    {{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 break-words">
                                    {{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $color = match ($plan->indicador) {
                                            'rojo' => 'bg-red-400',
                                            'amarillo' => 'bg-yellow-300',
                                            'verde' => 'bg-green-400',
                                            'finalizado' => 'bg-blue-400',
                                            default => 'bg-gray-300',
                                        };
                                    @endphp
                                    <span class="inline-block w-4 h-4 rounded-full {{ $color }}"
                                        title="{{ ucfirst($plan->indicador) }}"></span>
                                </td>
                                <td class="px-4 py-3 text-center space-y-2">
                                    <!-- Primer grupo: Editar / Eliminar -->
                                    <div class="flex justify-center gap-2 border-b border-gray-300 pb-2 mb-2">
                                        <button onclick='openEditModal(@json($plan))'
                                            class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-md text-xs hover:bg-yellow-200 transition shadow-sm">
                                            Editar
                                        </button>

                                        <form method="POST"
                                            action="{{ route('planes.eliminarConUsuarios', $plan->id) }}"
                                            onsubmit="return confirm('¿Está seguro de eliminar este plan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-100 text-red-800 px-3 py-1.5 rounded-md text-xs hover:bg-red-200 transition shadow-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Segundo grupo: Metas / Reporte / Finalizar -->
                                    <div class="flex justify-center gap-2 border-b border-gray-300 pb-2 mb-2">
                                        <a href="{{ route('plan.metas', $plan->id) }}"
                                            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-xs hover:bg-blue-200 transition shadow-sm">
                                            Metas/Objetivos Estratégicos
                                        </a>
                                        <a href="{{ route('planes.reporte', $plan->id) }}"
                                            class="bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-md text-xs hover:bg-indigo-200 transition shadow-sm">
                                            Ver Reporte
                                        </a>
                                        <form method="POST" action="{{ route('planes.finalizar', $plan->id) }}">
                                            @csrf
                                            @php
                                                $esFinalizado = $plan->indicador === 'finalizado';
                                                $clasesBoton = $esFinalizado
                                                    ? 'bg-orange-100 text-orange-800 hover:bg-orange-200'
                                                    : 'bg-teal-100 text-teal-800 hover:bg-teal-200';
                                            @endphp
                                            <button type="submit"
                                                class="{{ $clasesBoton }} px-3 py-1.5 rounded-md text-xs transition shadow-sm">
                                                {{ $esFinalizado ? 'Reanudar Plan' : 'Finalizar Plan' }}
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Tercer grupo: Respaldo -->
                                    <div class="flex justify-center gap-2">
                                        @if (!$plan->backup)
                                            <form method="POST" action="{{ route('planes.backup', $plan->id) }}"
                                                onsubmit="return confirm('¿Seguro que quieres crear un respaldo de este plan? Esta acción no se puede repetir.')">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-md text-xs hover:bg-purple-200 transition shadow-sm">
                                                    Crear Respaldo
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('planes.verBackup', $plan->backup->id) }}"
                                                class="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-md text-xs hover:bg-gray-200 transition shadow-sm">
                                                Ver Respaldo
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-4 text-center text-gray-500">No hay planes
                                    registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <br>
                @auth
                    @php
                        switch (auth()->user()->tipo_usuario) {
                            case 'encargado_institucion':
                                $rutaInicio = route('institucion.ver', auth()->user()->idInstitucion);
                                break;
                            case 'administrador':
                                $rutaInicio = route('instituciones.index');
                                break;
                            default:
                                $rutaInicio = '#';
                        }
                    @endphp

                    @if ($rutaInicio !== '#')
                        <div class="mt-8">
                            <a href="{{ $rutaInicio }}"
                                class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Volver a instituciones
                            </a>
                        </div>
                    @endif
                @endauth

            </div>
        </div>
    </div>

    <!-- modal para ver completo ejes y objetivos -->
    <div id="modalTextoLargo"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600" onclick="cerrarModalTexto()">
                ✕
            </button>
            <h3 id="modalTextoTitulo" class="text-lg font-bold mb-4 text-indigo-700"></h3>
            <div id="modalTextoContenido" class="text-gray-800 whitespace-pre-wrap"></div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative overflow-y-auto max-h-[90vh]">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="document.getElementById('modal').classList.add('hidden')">
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Nuevo Plan Estratégico</h2>

            <form id="formPlan" method="POST" action="{{ route('planes.store') }}">
                @csrf
                <input type="hidden" name="institucion_id" value="{{ $institucion->id }}">

                <!-- Institución (solo visible, no editable) -->
                <div class="mb-4">
                    <label class="block font-medium">Institución</label>
                    <input type="text" value="{{ $institucion->nombre_institucion }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" disabled>
                </div>


                <!-- Departamento -->
                <div class="mb-4">
                    <label for="idDepartamento" class="block font-medium">Departamento</label>
                    <select name="idDepartamento" id="idDepartamento" class="w-full border rounded px-3 py-2"
                        required>
                        <option value="">Seleccione un departamento</option>
                        @foreach ($institucion->departamentos as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->departamento }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Agregar Departamento -->
                <button type="button" onclick="abrirModalDepartamento()"
                    class="mt-2 inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                    + Agregar departamento
                </button>


                <!-- Nombre -->
                <div class="mb-4">
                    <label for="nombre_plan_estrategico" class="block font-medium">Nombre del Plan</label>
                    <input type="text" name="nombre_plan_estrategico" class="w-full border rounded px-3 py-2"
                        required>
                </div>

                <!-- Ejes Estratégicos -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ejes Estratégicos <span
                            class="text-red-500">*</span></label>
                    <div id="contenedorEjes" class="space-y-2 max-h-60 overflow-y-auto"></div>
                    <button type="button" onclick="agregarEje()"
                        class="mt-3 inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        + Añadir otro eje
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Máximo 2000 caracteres por eje</p>
                </div>

                <!-- Objetivos -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objetivos</label>
                    <div id="contenedorObjetivos" class="space-y-2 max-h-60 overflow-y-auto">
                        <!-- DEJAR COMPLETAMENTE VACÍO -->
                    </div>
                    <button type="button" onclick="agregarObjetivo()"
                        class="mt-3 inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        + Añadir objetivo
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Máximo 2000 caracteres por objetivo</p>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <label for="fecha_inicio" class="block font-medium">Inicio</label>
                        <input type="date" name="fecha_inicio" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="w-1/2">
                        <label for="fecha_fin" class="block font-medium">Fin</label>
                        <input type="date" name="fecha_fin" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>

                <!-- Responsable -->
                <div class="mb-4">
                    <label for="responsable" class="block font-medium">Responsable</label>
                    <select name="responsable" id="responsable" class="w-full border rounded px-3 py-2"
                        data-encargado-id="{{ Auth::id() }}"
                        data-encargado-nombre="{{ Auth::user()->name ?? Auth::user()->nombre_usuario }}">
                        <option value="">Seleccione un responsable</option>
                    </select>

                    <!-- Mensaje cuando no hay departamento seleccionado -->
                    <div id="mensajeSeleccioneDepto" class="text-red-600 font-semibold mt-2" style="display: none;">
                        Seleccione un departamento para ver más usuarios
                    </div>

                    <!-- Mensaje cuando no hay usuarios disponibles -->
                    <div id="mensajeNoUsuarios" class="text-red-600 font-semibold mt-2" style="display: none;">
                        No hay usuarios registrados/disponibles (solo el encargado) agregue uno si quiere asignar otro
                    </div>

                    <!-- Botón para abrir modal de usuario -->
                    <button type="button" onclick="abrirModalUsuario()"
                        class="mt-2 inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                        + Usuario
                    </button>
                </div>


                <div class="flex justify-end gap-2">
                    {{-- Botón Cancelar --}}
                    <button type="button" onclick="document.getElementById('modal').classList.add('hidden')"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Cancelar
                    </button>

                    {{-- Botón Guardar --}}
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Guardar Plan
                    </button>
                </div>
        </div>
        </form>
    </div>
    </div>

    <!-- Modal Usuario -->
    <div id="modalUsuario" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="cerrarModalUsuario()">✕</button>

            <h2 class="text-xl font-bold mb-4">Registrar Nuevo Usuario</h2>

            <form id="formUsuario">
                @csrf

                <!-- Institución (no editable) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Institución</label>
                    <input type="text" value="{{ $institucion->nombre_institucion }}"
                        class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-100 text-gray-700" disabled>
                    <input type="hidden" name="idInstitucion" id="institucion_usuario"
                        value="{{ $institucion->id }}">
                </div>

                <!-- Departamento -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Departamento</label>
                    <select id="departamento_usuario" name="idDepartamento" required
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm">
                        <option value="">Seleccione un departamento</option>
                        @foreach ($institucion->departamentos as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->departamento }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre del Usuario</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <div class="relative mt-1">
                        <!-- ID ÚNICO: passwordModal -->
                        <input type="password" name="password" id="passwordModal"
                            class="block w-full rounded-md border border-gray-300 shadow-sm pr-10" required>
                        <button type="button"
                            onclick="togglePasswordModal('passwordModal', 'eye-open-modal', 'eye-closed-modal')"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                            <!-- Icono de ojo abierto -->
                            <svg id="eye-open-modal" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <!-- Icono de ojo cerrado -->
                            <svg id="eye-closed-modal" class="h-5 w-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
                    <select name="tipo_usuario" disabled
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
                        <option value="responsable_plan" selected>Responsable de Plan Estratégico</option>
                    </select>
                    <!-- Campo oculto para enviar 'responsable_plan' al servidor -->
                    <input type="hidden" name="tipo_usuario" value="responsable_plan">
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="cerrarModalUsuario()"
                        class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Departamento -->
    <div id="modalDepartamento"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative overflow-y-auto max-h-[90vh]">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="cerrarModalDepartamento()">✕</button>

            <h2 class="text-xl font-bold mb-4">Registrar Departamento</h2>

            <form id="formDepartamento" method="POST" action="{{ route('departamentos.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block font-medium">Institución</label>
                    <input type="text" value="{{ $institucion->nombre_institucion }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" disabled>
                    <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Nombre del Departamento</label>
                    <input type="text" name="departamento" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Encargado del Departamento (opcional)</label>
                    <select name="idEncargadoDepartamento" id="idEncargadoDepartamento"
                        class="w-full border rounded px-3 py-2">
                        <option value="">Seleccione un encargado</option>
                    </select>

                    <button type="button" onclick="abrirModalUsuarioDepto()"
                        class="mt-2 inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                        + Agregar nuevo usuario
                    </button>

                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="cerrarModalDepartamento()"
                        class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal crear usuarios para departamento -->
    <div id="modalUsuarioDepto"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="cerrarModalUsuarioDepto()">✕</button>
            <h2 class="text-xl font-bold mb-4">Registrar Usuario</h2>

            <form id="formUsuarioDepto" method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block font-medium">Nombre</label>
                    <input type="text" name="nombre_usuario" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block font-medium">Contraseña</label>
                    <div class="relative mt-1">
                        <!-- ID ÚNICO: passwordDepto -->
                        <input type="password" name="password" id="passwordDepto"
                            class="block w-full rounded-md border border-gray-300 shadow-sm pr-10" required>
                        <button type="button"
                            onclick="togglePassword('passwordDepto', 'eye-open-depto', 'eye-closed-depto')"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                            <!-- Icono de ojo abierto -->
                            <svg id="eye-open-depto" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <!-- Icono de ojo cerrado -->
                            <svg id="eye-closed-depto" class="h-5 w-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block font-medium">Tipo</label>
                    <input type="text" value="Encargado de Departamento"
                        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" disabled>
                    <input type="hidden" name="tipo_usuario" value="encargado_departamento">
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="cerrarModalUsuarioDepto()"
                        class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ==================== DATOS BASE ====================
        const departamentos = @json($institucion->departamentos);

        // ==================== HELPERS ====================
        function crearEntrada(name, value = '', removable = true) {
            const div = document.createElement('div');
            div.className = 'relative flex items-center mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = name;
            input.value = value ?? '';
            input.maxLength = 2000;
            input.className =
                'w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
            input.placeholder = name.includes('ejes') ? 'Ingrese el eje estratégico' : 'Describa el objetivo';
            input.required = name.includes('ejes_estrategicos');

            // Prevenir Enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });

            div.appendChild(input);

            if (removable) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ml-2 text-red-500 hover:text-red-700';
                btn.innerHTML = `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                         </svg>`;
                btn.onclick = function() {
                    if (confirm('¿Eliminar este campo?')) {
                        div.remove();
                    }
                };
                div.appendChild(btn);
            }

            return div;
        }

        // ==================== LIMPIAR CAMPOS DINÁMICOS ====================
        function limpiarCamposDinamicos() {
            // Restablecer ejes: solo el primer campo vacío
            const contenedorEjes = document.getElementById('contenedorEjes');
            if (contenedorEjes) {
                contenedorEjes.innerHTML = '';
                contenedorEjes.appendChild(crearEntrada('ejes_estrategicos[]', '', false));
            }

            // OBJETIVOS: Limpiar COMPLETAMENTE sin agregar ninguno
            const contenedorObjetivos = document.getElementById('contenedorObjetivos');
            if (contenedorObjetivos) {
                contenedorObjetivos.innerHTML = ''; // Dejar vacío
            }
        }

        // ==================== ELIMINAR CAMPO ====================
        function eliminarCampo(boton) {
            boton.parentElement.remove();
        }

        // ==================== CARGA DE USUARIOS ====================
        async function cargarUsuarios(departamentoId) {
            try {
                const res = await fetch(`/departamentos/${departamentoId}/usuarios-disponibles`);
                if (!res.ok) throw new Error('Error cargando usuarios');
                return await res.json();
            } catch (e) {
                console.error("Error al cargar usuarios:", e);
                return [];
            }
        }

        function agregarEncargado(select) {
            const encargadoId = select.dataset.encargadoId;
            const encargadoNombre = select.dataset.encargadoNombre;

            if (!encargadoId) return;

            if (![...select.options].some(o => o.value == encargadoId)) {
                const optionEncargado = document.createElement('option');
                optionEncargado.value = encargadoId;
                optionEncargado.textContent = `${encargadoNombre} (Encargado)`;
                optionEncargado.selected = true;
                select.appendChild(optionEncargado);
            }
        }

        // ==================== EVENTOS - RESPONSABLE ====================
        document.getElementById('idDepartamento').addEventListener('change', async function() {
            const departamentoId = this.value;
            const selectResponsable = document.getElementById('responsable');
            const mensajeNoUsuarios = document.getElementById('mensajeNoUsuarios');
            const mensajeSeleccioneDepto = document.getElementById('mensajeSeleccioneDepto');

            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';
            mensajeNoUsuarios.style.display = 'none';
            mensajeSeleccioneDepto.style.display = 'none';

            selectResponsable.dataset.encargadoId = '{{ Auth::id() }}';
            selectResponsable.dataset.encargadoNombre =
                '{{ Auth::user()->name ?? Auth::user()->nombre_usuario }}';
            agregarEncargado(selectResponsable);

            if (departamentoId) {
                const usuarios = await cargarUsuarios(departamentoId);

                if (usuarios.length === 0) {
                    mensajeNoUsuarios.style.display = 'block';
                } else {
                    usuarios.forEach(u => {
                        const option = document.createElement('option');
                        option.value = u.id;
                        option.textContent = u.nombre_usuario;
                        selectResponsable.appendChild(option);
                    });
                }
            } else {
                mensajeSeleccioneDepto.style.display = 'block';
            }
        });

        // ==================== OBJETIVOS ====================
        function agregarObjetivo() {
            const contenedor = document.getElementById('contenedorObjetivos');
            contenedor.appendChild(crearEntrada('objetivos[]', '', true));
        }

        // ==================== EJES ====================
        function agregarEje() {
            const contenedor = document.getElementById('contenedorEjes');
            contenedor.appendChild(crearEntrada('ejes_estrategicos[]', '', true));
        }

        // ==================== MODAL PLAN ====================
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnNuevoPlan').addEventListener('click', () => {
                limpiarCamposDinamicos();

                const modal = document.getElementById('modal');
                const form = document.getElementById('formPlan');

                form.reset();
                form.institucion_id.value = '{{ $institucion->id }}';

                const selectResponsable = document.getElementById('responsable');
                selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';

                document.getElementById('mensajeNoUsuarios').style.display = 'none';

                agregarEncargado(selectResponsable);

                modal.classList.remove('hidden');
            });
        });

        // ==================== EDITAR PLAN ====================
        async function openEditModal(plan) {
            const modal = document.getElementById('modal');
            const form = document.getElementById('formPlan');

            form.reset();
            form.action = `/planes/${plan.id}`;

            let methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.value = 'PUT';
            } else {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }

            form.querySelector('input[name="nombre_plan_estrategico"]').value = plan.nombre_plan_estrategico;
            form.querySelector('input[name="fecha_inicio"]').value = plan.fecha_inicio;
            form.querySelector('input[name="fecha_fin"]').value = plan.fecha_fin;
            form.querySelector('input[name="institucion_id"]').value = plan.idInstitucion;

            const departamentoSelect = form.querySelector('select[name="idDepartamento"]');
            departamentoSelect.value = plan.idDepartamento;

            // EJES - CORREGIDO: JSON.parse en lugar de split
            const contenedorEjes = document.getElementById('contenedorEjes');
            contenedorEjes.innerHTML = '';
            let ejes = [];

            if (plan.ejes_estrategicos) {
                try {
                    ejes = JSON.parse(plan.ejes_estrategicos);
                } catch {
                    // Fallback para datos antiguos
                    ejes = plan.ejes_estrategicos.split(',').map(e => e.trim()).filter(Boolean);
                }
            }

            ejes.forEach((eje, idx) => {
                if (eje) contenedorEjes.appendChild(crearEntrada('ejes_estrategicos[]', eje, idx > 0));
            });

            // OBJETIVOS
            const contenedorObjetivos = document.getElementById('contenedorObjetivos');
            contenedorObjetivos.innerHTML = '';
            let objetivos = [];

            if (plan.objetivos) {
                try {
                    objetivos = typeof plan.objetivos === 'string' ? JSON.parse(plan.objetivos) : plan.objetivos;
                } catch {
                    objetivos = [];
                }
            }

            objetivos.forEach(obj => {
                if (obj) contenedorObjetivos.appendChild(crearEntrada('objetivos[]', obj, true));
            });

            // RESPONSABLE
            const mensajeSeleccioneDepto = document.getElementById('mensajeSeleccioneDepto');
            const mensajeNoUsuarios = document.getElementById('mensajeNoUsuarios');
            mensajeSeleccioneDepto.style.display = 'none';
            mensajeNoUsuarios.style.display = 'none';

            const selectResponsable = document.getElementById('responsable');
            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';

            const responsablePlanId = plan.responsable_id || plan.idUsuario || plan.usuario_id || null;

            const event = new Event('change', {
                bubbles: true
            });
            departamentoSelect.dispatchEvent(event);

            setTimeout(() => {
                if (responsablePlanId) {
                    if (!Array.from(selectResponsable.options).some(opt => opt.value == responsablePlanId)) {
                        const option = document.createElement('option');
                        option.value = responsablePlanId;
                        option.textContent = plan.responsable?.nombre_usuario || 'Usuario del plan';
                        selectResponsable.appendChild(option);
                    }
                    selectResponsable.value = responsablePlanId;
                }
            }, 300);

            modal.classList.remove('hidden');
        }

        // ==================== MODAL USUARIO (PRIMER MODAL) ====================
        function abrirModalUsuario() {
            document.getElementById('modalUsuario').classList.remove('hidden');
        }

        function cerrarModalUsuario() {
            document.getElementById('modalUsuario').classList.add('hidden');
            document.getElementById('formUsuario').reset();
        }

        function togglePasswordModal(inputId, openId, closedId) {
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(openId);
            const eyeClosed = document.getElementById(closedId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        document.getElementById('formUsuario')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            try {
                const response = await fetch("{{ route('usuarios.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: data
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error desconocido');
                }

                const nuevoUsuario = await response.json();
                const select = document.getElementById('responsable');
                const option = document.createElement('option');
                option.value = nuevoUsuario.usuario.id;
                option.textContent = `${nuevoUsuario.usuario.nombre_usuario} (Responsable de Plan)`;
                option.selected = true;
                select.appendChild(option);

                document.getElementById('mensajeNoUsuarios').style.display = 'none';
                cerrarModalUsuario();
            } catch (error) {
                alert('Error al registrar usuario:\n' + error.message);
            }
        });

        // ==================== MODAL DEPARTAMENTO ====================
        function abrirModalDepartamento() {
            const m = document.getElementById('modalDepartamento');
            m.classList.remove('hidden');

            const sel = document.getElementById('idEncargadoDepartamento');
            if (!sel) return;
            sel.innerHTML = '<option value="">Seleccione un encargado</option>';

            fetch(`/instituciones/{{ $institucion->id }}/usuarios-disponibles`)
                .then(r => r.ok ? r.json() : Promise.resolve([]))
                .then(list => {
                    list.forEach(u => {
                        const o = document.createElement('option');
                        o.value = u.id;
                        o.textContent = `${u.nombre_usuario} (${u.email})`;
                        sel.appendChild(o);
                    });
                })
                .catch(() => {});
        }

        function cerrarModalDepartamento() {
            document.getElementById('modalDepartamento').classList.add('hidden');
            document.getElementById('formDepartamento').reset();
        }

        document.getElementById('formDepartamento')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: data
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'No se pudo crear el departamento');
                }

                const out = await res.json();
                const selDepPlan = document.getElementById('idDepartamento');
                const optPlan = document.createElement('option');
                optPlan.value = out.departamento.id;
                optPlan.textContent = out.departamento.departamento;
                selDepPlan.appendChild(optPlan);
                selDepPlan.value = out.departamento.id;

                const selDepUsuario = document.getElementById('departamento_usuario');
                if (selDepUsuario) {
                    const optUsuario = document.createElement('option');
                    optUsuario.value = out.departamento.id;
                    optUsuario.textContent = out.departamento.departamento;
                    selDepUsuario.appendChild(optUsuario);
                }

                cerrarModalDepartamento();
            } catch (err) {
                if (err instanceof SyntaxError) {
                    window.location.reload();
                    return;
                }
                alert(err.message);
            }
        });

        // ==================== MODAL USUARIO (desde Departamento) ====================
        function abrirModalUsuarioDepto() {
            document.getElementById('modalUsuarioDepto').classList.remove('hidden');
        }

        function cerrarModalUsuarioDepto() {
            document.getElementById('modalUsuarioDepto').classList.add('hidden');
            document.getElementById('formUsuarioDepto').reset();
        }

        function togglePassword(inputId, openId, closedId) {
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(openId);
            const eyeClosed = document.getElementById(closedId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        document.getElementById('formUsuarioDepto')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: data
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    throw new Error(err.message || 'Error al crear usuario');
                }

                const nuevoUsuario = await response.json();
                const select = document.getElementById('idEncargadoDepartamento');
                const option = document.createElement('option');
                option.value = nuevoUsuario.usuario.id;
                option.textContent = `${nuevoUsuario.usuario.nombre_usuario} (${nuevoUsuario.usuario.email})`;
                option.selected = true;
                select.appendChild(option);

                cerrarModalUsuarioDepto();
            } catch (error) {
                alert(error.message);
            }
        });

        // ==================== VALIDACIÓN DE FECHAS ====================
        document.getElementById("formPlan").addEventListener("submit", function(e) {
            const inicio = document.querySelector("[name='fecha_inicio']").value;
            const fin = document.querySelector("[name='fecha_fin']").value;

            if (inicio && fin && new Date(inicio) > new Date(fin)) {
                e.preventDefault();
                mostrarAlerta("⚠️ La fecha de inicio no puede ser mayor que la fecha de fin.");
            }
        });

        function mostrarAlerta(mensaje) {
            let alerta = document.getElementById("alertaFechas");
            if (!alerta) {
                alerta = document.createElement("div");
                alerta.id = "alertaFechas";
                alerta.className = "bg-yellow-100 text-yellow-800 border border-yellow-400 px-4 py-2 rounded mb-4";
                const form = document.getElementById("formPlan");
                form.insertBefore(alerta, form.firstChild);
            }
            alerta.textContent = mensaje;
        }

        // ==================== MODAL TEXTO LARGO ====================
        function formatearContenidoModal(contenido) {
            if (!contenido) return 'No hay contenido';

            // Si es un array o JSON de array
            try {
                const array = typeof contenido === 'string' ? JSON.parse(contenido) : contenido;
                if (Array.isArray(array)) {
                    return array.map((item, index) => `${index + 1}. ${item}`).join('\n\n');
                }
            } catch (e) {
                // Si falla el parseo, devolver como texto plano
                return contenido.toString();
            }

            return contenido.toString();
        }

        function abrirModalTexto(titulo, contenido) {
            document.getElementById('modalTextoTitulo').textContent = titulo;
            document.getElementById('modalTextoContenido').textContent = formatearContenidoModal(contenido);
            document.getElementById('modalTextoLargo').classList.remove('hidden');
        }

        function cerrarModalTexto() {
            document.getElementById('modalTextoLargo').classList.add('hidden');
        }

        // ==================== INICIALIZACIÓN GLOBAL ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar solo EJES, NO objetivos
            const contenedorEjes = document.getElementById('contenedorEjes');
            if (contenedorEjes && contenedorEjes.children.length === 0) {
                contenedorEjes.appendChild(crearEntrada('ejes_estrategicos[]', '', false));
            }

            if (contenedorObjetivos && contenedorObjetivos.children.length === 0) {
                contenedorObjetivos.appendChild(crearEntrada('objetivos[]', '', true));
            }
        });
    </script>

</x-app-layout>
