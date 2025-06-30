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
                    <h1 class="text-2xl font-bold">Lista de Actividades de la Meta: "{{ $meta->nombre_meta }}"</h1>

                    <!-- Botón para agregar un nuevo registro -->
                    <div x-data="{ modalOpen: false }">
                        <button @click="modalOpen = true"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Agregar Nueva Actividad
                        </button>

                        <!-- Modal -->
                        <div x-show="modalOpen"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                            <div @click.away="modalOpen = false"
                                class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                <h2 class="text-lg font-semibold mb-4">Registrar Nueva Actividad</h2>
                                @include('actividades.create', [
                                    'departamentos' => $departamentos,
                                    'metas' => $metas,
                                    'usuarios' => $usuarios,
                                ])
                            </div>
                        </div>
                        <a href="{{ route('meta.resumen_seguimientos', $meta->id) }}"
                            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 mb-4 inline-block">
                            Ver resumen general de seguimientos
                        </a>
                    </div>

                </div>

                @if (session('success'))
                    <div class="mb-4 text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-red-600">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tabla de actividades -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre de la Actividad</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Obejtivos</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Inicio</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Finalizacion</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Resultados Esperados</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unidad Encargada</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Seguimiento
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($actividades as $actividad)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->usuario->nombre_usuario }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->nombre_actividad }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->objetivos }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->fecha_inicio }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->fecha_fin }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->resultados_esperados }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $actividad->unidad_encargada }}</td>
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

                                                    @include('actividades.edit', [
                                                        'action' => route('actividades.update', $actividad->id),
                                                        'isEdit' => true,
                                                        'actividad' => $actividad,
                                                        'departamentos' => $departamentos,
                                                    ])
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Eliminar -->
                                        <div x-data="{ confirmDelete: false }" class="inline-block">
                                            <!-- Botón que abre el modal -->
                                            <button @click="confirmDelete = true"
                                                class="text-red-600 hover:text-red-900">
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
                                                    <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar
                                                        esta
                                                        Actividad?</p>

                                                    <div class="flex justify-end space-x-3">
                                                        <button @click="confirmDelete = false"
                                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                            Cancelar
                                                        </button>

                                                        <form method="POST"
                                                            action="{{ route('actividades.destroy', $actividad->id) }}">
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

                                    </td>
                                    <td class="text-center px-4 py-2">
                                        <div class="flex flex-col items-center space-y-2"> {{-- Contenedor flex para alinear verticalmente y añadir espacio --}}
                                            <button onclick="abrirModalCrearSeguimiento({{ $actividad->id }})"
                                                class="bg-purple-300 text-purple-800 px-3 py-1 rounded hover:bg-purple-400 transition text-sm">
                                                {{-- Morado pastel --}}
                                                Seguimiento
                                            </button>
                                            <button onclick="mostrarSeguimientos({{ $actividad->id }})"
                                                class="bg-blue-300 text-blue-800 px-3 py-1 rounded hover:bg-blue-400 transition text-sm">
                                                {{-- Azul pastel (similar al anterior) --}}
                                                Ver Seguimientos
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach

                            @if ($actividades->isEmpty())
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
        </script>






</x-app-layout>
