<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold mb-6">Crear Plan Estratégico para {{ $institucion->nombre_institucion }}</h2>

        <form method="POST" action="{{ route('planes.store') }}">
            @csrf

            <!-- Departamento -->
            <div class="mb-4">
                <label for="idDepartamento" class="block font-medium">Departamento</label>
                <select name="idDepartamento" id="idDepartamento" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccione un departamento</option>
                    @foreach ($institucion->departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nombre del Plan -->
            <div class="mb-4">
                <label for="nombre_plan_estrategico" class="block font-medium">Nombre del Plan</label>
                <input type="text" name="nombre_plan_estrategico" id="nombre_plan_estrategico"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Ejes Estratégicos -->
            <div class="mb-4">
                <label for="ejes_estrategicos" class="block font-medium">Ejes Estratégicos</label>
                <input type="text" name="ejes_estrategicos" id="ejes_estrategicos"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Fechas -->
            <div class="mb-4">
                <label for="fecha_inicio" class="block font-medium">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full border rounded px-3 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label for="fecha_fin" class="block font-medium">Fecha de Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="w-full border rounded px-3 py-2" required>
            </div>


            <!-- Responsable -->
            <div class="mb-4">
                <label for="responsable" class="block font-medium">Responsable</label>

                <select name="responsable" id="responsable" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccione un usuario</option>
                    @foreach ($usuariosDisponibles as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->nombre_usuario }}</option>
                    @endforeach
                </select>

                <!-- Mensaje "No hay usuarios" solo visible si no hay opciones (más que la opción vacía) -->
                <div id="mensajeNoUsuarios" class="text-red-600 font-semibold mt-2"
                    style="{{ count($usuariosDisponibles) > 0 ? 'display:none;' : '' }}">
                    No hay usuarios disponibles.
                </div>

                <!-- Botón para abrir modal para agregar usuario, siempre visible -->
                <button type="button" onclick="abrirModalUsuario()" class="mt-2 text-blue-600 underline">
                    Agregar usuario
                </button>
            </div>


            <!-- Botón -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Guardar Plan Estratégico
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Usuario -->
    <div id="modalUsuario" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="cerrarModalUsuario()">✕</button>

            <h2 class="text-xl font-bold mb-4">Registrar Nuevo Usuario</h2>

            <form id="formUsuario">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Institución</label>
                    <input type="text" disabled value="{{ $institucion->nombre_institucion }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm bg-gray-100 text-gray-700">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre del Usuario</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm" required>
                </div>


                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Departamento</label>
                    <select name="idDepartamento" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
                        <option value="">Seleccione un departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
                    <select name="tipo_usuario" required
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm">
                        <option value="Administrador">Administrador</option>
                        <option value="Colaborador">Colaborador</option>
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

    <!-- Script para manejar el modal de usuario -->
    <script>
        function abrirModalUsuario() {
            document.getElementById('modalUsuario').classList.remove('hidden');
        }

        function cerrarModalUsuario() {
            document.getElementById('modalUsuario').classList.add('hidden');
            document.getElementById('formUsuario').reset();
        }

        const formUsuario = document.getElementById('formUsuario');
        formUsuario.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const data = new FormData(form);

            try {
                const response = await fetch("{{ route('usuarios.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json' // <-- esto ayuda a decir que queremos JSON
                    },
                    body: data
                });

                if (!response.ok) {
                    // Intenta leer el mensaje de error en JSON
                    const errorData = await response.json();
                    let message = 'Error desconocido';

                    if (errorData.errors) {
                        message = Object.values(errorData.errors).flat().join('\n');
                    } else if (errorData.message) {
                        message = errorData.message;
                    }
                    throw new Error(message);
                }

                const nuevoUsuario = await response.json();

                // Agregar al select de responsables
                const select = document.getElementById('responsable');
                const option = document.createElement('option');
                option.value = nuevoUsuario.usuario.id;
                option.textContent = nuevoUsuario.usuario.nombre_usuario;
                option.selected = true;
                select.appendChild(option);

                // Ocultar el mensaje "No hay usuarios"
                const mensaje = document.getElementById('mensajeNoUsuarios');
                if (mensaje) {
                    mensaje.style.display = 'none';
                }
                cerrarModalUsuario();

            } catch (error) {
                alert('Ocurrió un error al registrar al usuario. Revisa los datos:\n' + error.message);
                console.error('Error al registrar usuario:', error);
            }
        });
    </script>
    <!-- Scrip para filtrar usuarios por departamento -->
    <script>
        document.getElementById('idDepartamento').addEventListener('change', async function() {
            const departamentoId = this.value;
            const select = document.getElementById('responsable');
            const mensaje = document.getElementById('mensajeNoUsuarios');

            // Limpiar select actual
            select.innerHTML = '<option value="">Seleccione un usuario</option>';

            if (!departamentoId) {
                mensaje.style.display = 'block';
                return;
            }

            try {
                const response = await fetch(`/departamentos/${departamentoId}/usuarios-disponibles`);
                const usuarios = await response.json();

                if (usuarios.length === 0) {
                    mensaje.style.display = 'block';
                } else {
                    mensaje.style.display = 'none';
                    usuarios.forEach(usuario => {
                        const option = document.createElement('option');
                        option.value = usuario.id;
                        option.textContent = usuario.nombre_usuario;
                        select.appendChild(option);
                    });
                }
            } catch (err) {
                console.error("Error cargando usuarios:", err);
                mensaje.style.display = 'block';
            }
        });
    </script>

</x-app-layout>
