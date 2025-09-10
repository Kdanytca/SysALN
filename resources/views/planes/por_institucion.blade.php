<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Planes Estratégicos de: {{ $institucion->nombre_institucion }}
            </h2>
            <button id="btnNuevoPlan"
                class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-md hover:bg-green-200 shadow-sm transition text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Plan
            </button>
        </div>
    </x-slot>


    <br>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table
                    class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg overflow-hidden shadow text-sm text-gray-800">
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
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($planes as $plan)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-3 font-medium">{{ $plan->nombre_plan_estrategico }}</td>
                                <td class="px-4 py-3">{{ $plan->departamento->departamento ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $plan->responsable->nombre_usuario ?? 'Sin responsable' }}</td>
                                <td class="px-4 py-3">
                                    @foreach (explode(',', $plan->ejes_estrategicos) as $eje)
                                        <span
                                            class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mr-1 mb-1">
                                            {{ trim($eje) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @if ($plan->objetivos)
                                        @foreach (json_decode($plan->objetivos) as $objetivo)
                                            <div class="text-gray-800 text-xs mb-1">• {{ $objetivo }}</div>
                                        @endforeach
                                    @else
                                        <span class="italic text-gray-400">Sin objetivos</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $color = match ($plan->indicador) {
                                            'rojo' => 'bg-red-400',
                                            'amarillo' => 'bg-yellow-300',
                                            'verde' => 'bg-green-400',
                                            'finalizado' => 'bg-blue-400', // Aquí agregas el azul para finalizado
                                            default => 'bg-gray-300',
                                        };
                                    @endphp
                                    <span class="inline-block w-4 h-4 rounded-full {{ $color }}"
                                        title="{{ ucfirst($plan->indicador) }}"></span>
                                </td>
                                <td class="px-4 py-3 text-center space-y-2">
                                    <div class="flex justify-center gap-2">
                                        <button onclick='openEditModal(@json($plan))'
                                            class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-md text-xs hover:bg-yellow-200 transition shadow-sm">
                                            Editar
                                        </button>

                                        <form method="POST" action="{{ route('planes.destroy', $plan->id) }}"
                                            onsubmit="return confirm('¿Está seguro de eliminar este plan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-100 text-red-800 px-3 py-1.5 rounded-md text-xs hover:bg-red-200 transition shadow-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('plan.metas', $plan->id) }}"
                                            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-xs hover:bg-blue-200 transition shadow-sm">
                                            Ver Metas
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
                                                    ? 'bg-orange-100 text-orange-800 hover:bg-orange-200' // Reanudar
                                                    : 'bg-teal-100 text-teal-800 hover:bg-teal-200'; // Finalizar
                                            @endphp
                                            <button type="submit"
                                                class="{{ $clasesBoton }} px-3 py-1.5 rounded-md text-xs transition shadow-sm">
                                                {{ $esFinalizado ? 'Reanudar Plan' : 'Finalizar Plan' }}
                                            </button>
                                        </form>
                                    </div>

                                </td>
                                <td>

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
                                $rutaInicio = '#'; // o nada
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
                    <select name="idDepartamento" id="idDepartamento" class="w-full border rounded px-3 py-2" required>
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

                <!-- Ejes -->
                <div class="mb-4">
                    <label class="block font-medium">Ejes Estratégicos</label>

                    <div id="contenedorEjes">
                        <!-- Primer eje SIN X -->
                        <div class="relative mb-2">
                            <input type="text" name="ejes_estrategicos[]"
                                class="w-full border rounded px-3 py-2 pr-10" required>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-2">
                        <button type="button" onclick="agregarEje()"
                            class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                            + Eje
                        </button>
                    </div>
                </div>


                <!-- Objetivos -->
                <div class="mb-4">
                    <label class="block font-medium">Objetivos</label>

                    <div id="contenedorObjetivos"></div>

                    <div class="mt-2">
                        <button type="button" onclick="agregarObjetivo()"
                            class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                            + Objetivo
                        </button>
                    </div>
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

                    <div id="mensajeNoUsuarios" class="text-red-600 font-semibold mt-2" style="display: none;">
                        No hay usuarios disponibles.
                    </div>

                    <!-- Botón para abrir modal de usuario -->
                    <button type="button" onclick="abrirModalUsuario()"
                        class="mt-2 inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                        + Usuario
                    </button>



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
                    <input type="password" name="password" id="email"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>


                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
                    <select name="tipo_usuario" required
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm">
                        <option value="administrador">Administrador</option>
                        <option value="encargado_institucion">Encargado de Institución</option>
                        <option value="encargado_departamento">Encargado de Departamento</option>
                        <option value="responsable_plan">Responsable de Plan Estratégico</option>
                        <option value="responsable_meta">Responsable de Meta</option>
                        <option value="responsable_actividad">Responsable de Actividad</option>
                    </select>
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
                    <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
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
            div.className = 'relative mb-2';

            const requiredAttr = name.includes('ejes_estrategicos') ? 'required' : '';
            div.innerHTML = `
            <input type="text" name="${name}" value="${value ?? ''}"
                   class="w-full border rounded px-3 py-2 pr-10" ${requiredAttr}>
            ${removable ? `
                                                              <button type="button" class="btn-x absolute right-2 top-2 text-gray-500 hover:text-red-600"
                                                                      aria-label="Eliminar campo">&times;</button>` : ''}
        `;
            return div;
        }

        // Delegado para eliminar con la "X"
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-x')) {
                const parent = e.target.closest('.relative');
                if (parent) parent.remove();
            }
        });

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

            // Evitar duplicados
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
            const mensaje = document.getElementById('mensajeNoUsuarios');

            // Limpiar select
            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';
            mensaje.style.display = 'none';

            if (departamentoId) {
                const usuarios = await cargarUsuarios(departamentoId);

                if (usuarios.length === 0) {
                    mensaje.style.display = 'block';
                } else {
                    usuarios.forEach(u => {
                        const option = document.createElement('option');
                        option.value = u.id;
                        option.textContent = u.nombre_usuario;
                        selectResponsable.appendChild(option);
                    });
                }
            }

            // Reasignar atributos del usuario logueado y agregarlo
            selectResponsable.dataset.encargadoId = '{{ Auth::id() }}';
            selectResponsable.dataset.encargadoNombre =
                '{{ Auth::user()->name ?? Auth::user()->nombre_usuario }}';
            agregarEncargado(selectResponsable);
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Envolver el primer Eje si viniera como input suelto
            const contE = document.getElementById('contenedorEjes');
            if (contE) {
                const first = contE.querySelector('input[name="ejes_estrategicos[]"]');
                if (first && !first.closest('.relative')) {
                    const wrap = crearEntrada('ejes_estrategicos[]', first.value || '', /*removable*/ false);
                    contE.innerHTML = '';
                    contE.appendChild(wrap);
                }
            }

            // Agregar encargado preseleccionado al cargar
            const selectResponsable = document.getElementById('responsable');
            agregarEncargado(selectResponsable);
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

        // ==================== MODAL USUARIO (tal cual lo tenías) ====================
        function abrirModalUsuario() {
            document.getElementById('modalUsuario').classList.remove('hidden');
        }

        function cerrarModalUsuario() {
            document.getElementById('modalUsuario').classList.add('hidden');
            document.getElementById('formUsuario').reset();
        }

        const formUsuario = document.getElementById('formUsuario');
        formUsuario?.addEventListener('submit', async function(e) {
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
                    let message = errorData.message || 'Error desconocido';
                    if (errorData.errors) {
                        message = Object.values(errorData.errors).flat().join('\n');
                    }
                    throw new Error(message);
                }

                const nuevoUsuario = await response.json();
                const select = document.getElementById('responsable');
                const option = document.createElement('option');
                option.value = nuevoUsuario.usuario.id;
                option.textContent = nuevoUsuario.usuario.nombre_usuario;
                option.selected = true;
                select.appendChild(option);

                document.getElementById('mensajeNoUsuarios').style.display = 'none';
                cerrarModalUsuario();

            } catch (error) {
                alert('Ocurrió un error al registrar al usuario:\n' + error.message);
                console.error(error);
            }
        });

        // ==================== EDITAR PLAN (ajustado para usar "X") ====================
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
            form.querySelector('select[name="idDepartamento"]').value = plan.idDepartamento;

            // Ejes
            const contenedorEjes = document.getElementById('contenedorEjes');
            contenedorEjes.innerHTML = '';
            const ejes = plan.ejes_estrategicos.split(',').map(e => e.trim()).filter(Boolean);

            ejes.forEach((eje, idx) => {
                contenedorEjes.appendChild(crearEntrada('ejes_estrategicos[]', eje, /*removable*/ idx > 0));
            });

            // Objetivos
            const contenedorObjetivos = document.getElementById('contenedorObjetivos');
            contenedorObjetivos.innerHTML = '';
            if (plan.objetivos) {
                let objetivosArray = [];
                try {
                    objetivosArray = typeof plan.objetivos === 'string' ? JSON.parse(plan.objetivos) : plan.objetivos;
                } catch {
                    objetivosArray = [];
                }
                objetivosArray.forEach(obj => {
                    contenedorObjetivos.appendChild(crearEntrada('objetivos[]', obj ?? '', true));
                });
            }

            // Usuarios (responsable del plan)
            const selectResponsable = document.getElementById('responsable');
            const mensaje = document.getElementById('mensajeNoUsuarios');
            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';

            try {
                const response = await fetch(`/departamentos/${plan.idDepartamento}/usuarios-disponibles`);
                const usuarios = await response.json();

                usuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.id;
                    option.textContent = usuario.nombre_usuario;
                    selectResponsable.appendChild(option);
                });

                agregarEncargado(selectResponsable);
                mensaje.style.display = usuarios.length === 0 ? 'block' : 'none';

            } catch (err) {
                console.error('Error al cargar usuarios:', err);
                mensaje.style.display = 'block';
            }

            modal.classList.remove('hidden');
        }

        // ==================== MODAL DEPARTAMENTO (Nuevo) ====================
        function abrirModalDepartamento() {
            const m = document.getElementById('modalDepartamento');
            m.classList.remove('hidden');

            // Carga opcional de usuarios de la institución para "Encargado"
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
                .catch(() => {
                    /* silencioso */
                });
        }

        function cerrarModalDepartamento() {
            const m = document.getElementById('modalDepartamento');
            m.classList.add('hidden');
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

                // Esperamos { departamento: { id, departamento, ... } }
                const out = await res.json();

                // Actualizar el select de Departamentos del Plan
                const selDepPlan = document.getElementById('idDepartamento');
                const optPlan = document.createElement('option');
                optPlan.value = out.departamento.id;
                optPlan.textContent = out.departamento.departamento;
                selDepPlan.appendChild(optPlan);
                selDepPlan.value = out.departamento.id;

                // Actualizar también el select del modalUsuario
                const selDepUsuario = document.getElementById('departamento_usuario');
                if (selDepUsuario) {
                    const optUsuario = document.createElement('option');
                    optUsuario.value = out.departamento.id;
                    optUsuario.textContent = out.departamento.departamento;
                    selDepUsuario.appendChild(optUsuario);
                }

                cerrarModalDepartamento();
            } catch (err) {
                // Si tu controlador devuelve un redirect/HTML, recargamos
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

        document.getElementById('formUsuarioDepto')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    },
                    body: data
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    throw new Error(err.message || 'Error al crear usuario');
                }

                const nuevoUsuario = await response.json();

                // Lo agregamos al select de encargado del Departamento
                const select = document.getElementById('idEncargadoDepartamento');
                const option = document.createElement('option');
                option.value = nuevoUsuario.usuario.id;
                option.textContent = nuevoUsuario.usuario.nombre_usuario + ' (' + nuevoUsuario.usuario.email +
                    ')';
                option.selected = true;
                select.appendChild(option);

                cerrarModalUsuarioDepto();
            } catch (error) {
                alert(error.message);
            }
        });
    </script>


</x-app-layout>
