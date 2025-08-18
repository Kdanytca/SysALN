<div x-data="crearUsuario">
    <form @submit.prevent="enviarFormulario">
        @if ($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @csrf
    
        <div class="mb-4">
            <label class="block font-medium">Nombre del Usuario</label>
            <input type="text" name="nombre_usuario"
                class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium">Correo Electronico</label>
            <input type="email" name="email"
                class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium">Contraseña</label>
            <input type="password" name="password"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        @unless ($ocultarCamposRelacionados ?? false)
            @if (isset($institucion))
                {{-- Institución fija --}}
                <div class="mb-4">
                    <label class="block font-medium">Institución</label>
                    <input type="text" disabled value="{{ $institucion->nombre_institucion }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100">
                    <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">
                </div>

                {{-- Mostrar el campo de departamentos solo si estamos en la vista de metas --}}
                @if (isset($vistaMetas) && $vistaMetas)
                    <div class="mb-4">
                        <label class="block font-medium">Departamento</label>
                        <select name="idDepartamento" class="w-full border rounded px-3 py-2" required>
                            <option value="">Seleccione un departamento</option>
                            @foreach ($departamentos->where('idInstitucion', $institucion->id) as $departamento)
                                <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @else
                {{-- Selector de institución --}}
                <div class="mb-4">
                    <label class="block font-medium">Institución</label>
                    <select name="idInstitucion" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccione una institución</option>
                        @foreach ($instituciones as $institucion)
                            <option value="{{ $institucion->id }}">
                                {{ $institucion->nombre_institucion }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endunless
    
        <div class="mb-4">
            <label class="block font-medium">Tipo de Usuario</label>
            <select name="tipo_usuario" required class="w-full border rounded px-3 py-2">
                <option value="administrador">Administrador</option>
                <option value="encargado_institucion">Encargado de Institución</option>
                <option value="encargado_departamento">Encargado de Departamento</option>
                <option value="responsable_plan">Responsable de Plan Estratégico</option>
                <option value="responsable_meta">Responsable de Meta</option>
                <option value="responsable_actividad">Responsable de Actividad</option>
            </select>
        </div>
    
        <div class="flex justify-end">
            <button type="button" @click="{{ $closeModal }}"
                class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Cancelar
            </button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Guardar
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('crearUsuario', () => ({
            async enviarFormulario(event) {
                const form = event.target.closest('form');
                const formData = new FormData(form);

                try {
                    const response = await fetch("{{ route('usuarios.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const nuevoUsuario = data.usuario;

                        // 1. Agregar al <select> de encargados
                        const selectsEncargado = document.querySelectorAll('select[name="idEncargadoInstitucion"], select[name="idEncargadoDepartamento"], select[name="idEncargadoMeta"], select[name="idEncargadoActividad"]');
                        selectsEncargado.forEach(selectEncargado => {
                            const nuevaOpcion = document.createElement('option');
                            nuevaOpcion.value = nuevoUsuario.id;
                            nuevaOpcion.textContent = `${nuevoUsuario.nombre_usuario} (${nuevoUsuario.email})`;
                            nuevaOpcion.selected = true;
                            selectEncargado.appendChild(nuevaOpcion);
                        });

                        // 2. Cierra el modal
                        this.$dispatch('close-modal-usuario');

                        // 3. Limpia el formulario
                        form.reset();

                    } else {
                        const errorData = await response.json();
                        let mensaje = 'Errores:\n';
                        for (const key in errorData.errors) {
                            mensaje += '- ' + errorData.errors[key][0] + '\n';
                        }
                        alert(mensaje);
                    }
                } catch (error) {
                    console.error("Error al crear usuario:", error);
                    alert("Ocurrió un error inesperado.");
                }
            }
        }));
    });
</script>
