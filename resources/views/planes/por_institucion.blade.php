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
                        <input type="text" name="ejes_estrategicos[]" class="w-full border rounded px-3 py-2 mb-2"
                            required>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <button type="button" onclick="agregarEje()" class="text-sm text-blue-600 underline">
                            + Agregar otro eje
                        </button>
                        <button id="btnEliminarEje" type="button" onclick="eliminarUltimoEje()"
                            class="text-sm text-red-600 underline hidden">
                            🗑 Eliminar último eje
                        </button>
                    </div>
                </div>

                <!-- Objetivos -->
                <div class="mb-4">
                    <label class="block font-medium">Objetivos</label>
                    <div id="contenedorObjetivos">
                        <!-- Los objetivos se insertarán aquí dinámicamente -->
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <button type="button" onclick="agregarObjetivo()" class="text-sm text-blue-600 underline">
                            + Agregar objetivo
                        </button>
                        <button id="btnEliminarObjetivo" type="button" onclick="eliminarUltimoObjetivo()"
                            class="text-sm text-red-600 underline hidden">
                            🗑 Eliminar último objetivo
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
                    <select name="responsable" id="responsable" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccione un responsable</option>
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                    <div id="mensajeNoUsuarios" class="text-red-600 font-semibold mt-2" style="display: none;">
                        No hay usuarios disponibles.
                    </div>

                    <!-- Botón para abrir modal de usuario -->
                    <button type="button" onclick="abrirModalUsuario()" class="mt-2 text-blue-600 underline">
                        Agregar usuario
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



    <script>
        // Datos en JS para departamentos y usuarios de la institución actual 
        const departamentos = @json($institucion->departamentos);

        //traer usuarios por departamento
        async function cargarUsuarios(departamentoId) {
            try {
                const res = await fetch(`/departamentos/${departamentoId}/usuarios-disponibles`);
                if (!res.ok) throw new Error('Error cargando usuarios');
                return await res.json();
            } catch (e) {
                console.error(e);
                return [];
            }
        }

        // Filtrar usuarios cuando cambia departamento
        document.getElementById('idDepartamento').addEventListener('change', async function() {
            const departamentoId = this.value;
            const selectResponsable = document.getElementById('responsable');
            const mensaje = document.getElementById('mensajeNoUsuarios');

            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';
            mensaje.style.display = 'none';

            if (!departamentoId) return;

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
        });
        // Funciones para agregar y eliminar objetivos
        function agregarObjetivo() {
            const contenedor = document.getElementById('contenedorObjetivos');
            const input = document.createElement('div');
            input.classList.add('relative', 'mb-2');
            input.innerHTML = `
            <input type="text" name="objetivos[]" class="w-full border rounded px-3 py-2 pr-10" required>
        `;
            contenedor.appendChild(input);

            document.getElementById('btnEliminarObjetivo').classList.remove('hidden');
        }

        function eliminarUltimoObjetivo() {
            const contenedor = document.getElementById('contenedorObjetivos');
            if (contenedor.children.length > 0) {
                contenedor.removeChild(contenedor.lastChild);
            }
            if (contenedor.children.length === 0) {
                document.getElementById('btnEliminarObjetivo').classList.add('hidden');
            }
        }
        // Funciones para agregar y eliminar ejes
        function actualizarBotonEliminar() {
            const contenedor = document.getElementById('contenedorEjes');
            const inputs = contenedor.querySelectorAll('input');
            const btnEliminar = document.getElementById('btnEliminarEje');
            btnEliminar.classList.toggle('hidden', inputs.length <= 1);
        }

        function agregarEje() {
            const contenedor = document.getElementById('contenedorEjes');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'ejes_estrategicos[]';
            input.className = 'w-full border rounded px-3 py-2 mb-2';
            input.required = true;
            contenedor.appendChild(input);
            actualizarBotonEliminar();
        }

        function eliminarUltimoEje() {
            const contenedor = document.getElementById('contenedorEjes');
            const inputs = contenedor.querySelectorAll('input');
            if (inputs.length > 1) {
                contenedor.removeChild(inputs[inputs.length - 1]);
            }
            actualizarBotonEliminar();
        }

        document.addEventListener('DOMContentLoaded', () => {
            actualizarBotonEliminar();


            // Abrir modal con botón
            document.getElementById('btnNuevoPlan').addEventListener('click', () => {
                const modal = document.getElementById('modal');
                const form = document.getElementById('formPlan');

                form.reset();

                // Establecer institución fija (por si reset la borra)
                form.institucion_id.value = '{{ $institucion->id }}';

                // Limpiar select responsable
                document.getElementById('responsable').innerHTML =
                    '<option value="">Seleccione un responsable</option>';
                document.getElementById('mensajeNoUsuarios').style.display = 'none';

                modal.classList.remove('hidden');
            });
        });
        // ==================== MODAL USUARIO ====================
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

        // ==================== FILTRAR DEPARTAMENTOS POR INSTITUCIÓN (MODAL USUARIO) ====================
        document.getElementById('institucion_usuario')?.addEventListener('change', function() {
            const institucionId = this.value;
            const departamentoSelect = document.getElementById('departamento_usuario');

            departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';

            if (!institucionId) return;

            const institucion = instituciones.find(i => i.id == institucionId);
            if (institucion && institucion.departamentos.length > 0) {
                institucion.departamentos.forEach(dep => {
                    const option = document.createElement('option');
                    option.value = dep.id;
                    option.textContent = dep.departamento;
                    departamentoSelect.appendChild(option);
                });
            }
        });
        // ==================== MODAL PLAN: EDITAR ====================
        async function openEditModal(plan) {
            const modal = document.getElementById('modal');
            const form = document.getElementById('formPlan');

            // Resetear el formulario antes de editar
            form.reset();
            form.action = `/planes/${plan.id}`;

            // Agregar o reemplazar método PUT para la edición
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

            // Asignar valores a los campos
            form.querySelector('input[name="nombre_plan_estrategico"]').value = plan.nombre_plan_estrategico;
            form.querySelector('input[name="fecha_inicio"]').value = plan.fecha_inicio;
            form.querySelector('input[name="fecha_fin"]').value = plan.fecha_fin;
            form.querySelector('input[name="institucion_id"]').value = plan.idInstitucion;
            form.querySelector('select[name="idDepartamento"]').value = plan.idDepartamento;

            // Limpiar y cargar ejes estratégicos
            const contenedorEjes = document.getElementById('contenedorEjes');
            contenedorEjes.innerHTML = '';

            const ejes = plan.ejes_estrategicos.split(',');
            ejes.forEach(eje => {
                const div = document.createElement('div');
                div.className = 'relative mb-2';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'ejes_estrategicos[]';
                input.className = 'w-full border rounded px-3 py-2 pr-10'; // espacio para el botón
                input.required = true;
                input.value = eje.trim();

                const btnEliminar = document.createElement('button');
                btnEliminar.type = 'button';
                btnEliminar.className = 'absolute right-2 top-2 text-red-600 hover:text-red-800';
                btnEliminar.innerHTML = '&times;';
                btnEliminar.title = 'Eliminar eje estratégico';
                btnEliminar.onclick = () => div.remove();

                div.appendChild(input);
                div.appendChild(btnEliminar);
                contenedorEjes.appendChild(div);
            });

            actualizarBotonEliminar(); // Muestra el botón de eliminar eje si es necesario

            // Limpiar y cargar objetivos
            const contenedorObjetivos = document.getElementById('contenedorObjetivos');
            contenedorObjetivos.innerHTML = '';

            if (plan.objetivos) {
                let objetivosArray = [];
                try {
                    objetivosArray = typeof plan.objetivos === 'string' ? JSON.parse(plan.objetivos) : plan.objetivos;
                } catch {
                    objetivosArray = [];
                }

                objetivosArray.forEach(objetivo => {
                    const div = document.createElement('div');
                    div.className = 'relative mb-2';

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'objetivos[]';
                    input.className = 'w-full border rounded px-3 py-2 pr-10';
                    input.value = objetivo ?? '';

                    const btnEliminar = document.createElement('button');
                    btnEliminar.type = 'button';
                    btnEliminar.className = 'absolute right-2 top-2 text-red-600 hover:text-red-800';
                    btnEliminar.innerHTML = '&times;';
                    btnEliminar.title = 'Eliminar objetivo';
                    btnEliminar.onclick = () => div.remove();

                    div.appendChild(input);
                    div.appendChild(btnEliminar);
                    contenedorObjetivos.appendChild(div);
                });

            }


            // Filtrar usuarios disponibles por departamento
            const selectResponsable = document.getElementById('responsable');
            const mensaje = document.getElementById('mensajeNoUsuarios');
            selectResponsable.innerHTML = '<option value="">Seleccione un responsable</option>';

            try {
                const response = await fetch(`/departamentos/${plan.idDepartamento}/usuarios-disponibles`);
                const usuarios = await response.json();

                let usuarioActualIncluido = false;

                usuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.id;
                    option.textContent = usuario.nombre_usuario;
                    console.log('Plan responsable id:', plan.idUsuario);
                    console.log('Usuario id actual:', usuario.id);

                    if (usuario.id == plan.idUsuario) {
                        option.selected = true;
                        usuarioActualIncluido = true;
                    }
                    selectResponsable.appendChild(option);
                });

                // Si no está en la lista, agregar al responsable actual
                if (!usuarioActualIncluido) {
                    const res = await fetch(`/usuarios/${plan.idUsuario}`);
                    const data = await res.json();

                    const option = document.createElement('option');
                    option.value = data.id;
                    option.textContent = `${data.nombre_usuario} (actual)`;
                    option.selected = true;
                    selectResponsable.appendChild(option);
                }

                mensaje.style.display = usuarios.length === 0 ? 'block' : 'none';

            } catch (err) {
                console.error('Error al cargar usuarios:', err);
                mensaje.style.display = 'block';
            }

            // Mostrar modal
            modal.classList.remove('hidden');
        }
    </script>

</x-app-layout>
