<form id="formCrearUsuario" method="POST" action="{{ route('usuarios.store') }}">
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
        <label class="block font-medium">Nombre del Usuario <i class="text-red-500">*</i></label>
        <input type="text" name="nombre_usuario" id="nombre_usuario"
            class="w-full border rounded px-3 py-2" required>
        <p id="errorNombre" class="text-red-500 text-sm mt-1 hidden"></p>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Correo Electrónico <i class="text-red-500">*</i></label>
        <input type="email" name="email" id="email"
            class="w-full border rounded px-3 py-2" required>
        <p id="errorCorreo" class="text-red-500 text-sm mt-1 hidden"></p>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Contraseña <i class="text-red-500">*</i></label>
        <input type="password" name="password"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Institución</label>
        <select name="idInstitucion" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $institucion)
                <option value="{{ $institucion->id }}">{{ $institucion->nombre_institucion }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Departamento</label>
        <select name="idDepartamento" class="w-full border rounded px-3 py-2">
            <option value="">Seleccione un departamento</option>
            @foreach ($departamentos as $departamento)
                <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Tipo de Usuario <i class="text-red-500">*</i></label>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputNombre = document.getElementById('nombre_usuario');
    const inputCorreo = document.getElementById('email');
    const errorNombre = document.getElementById('errorNombre');
    const errorCorreo = document.getElementById('errorCorreo');
    const form = document.getElementById('formCrearUsuario');

    async function verificarCampo(campo, valor, errorElemento) {
        if (!valor.trim()) {
            errorElemento.classList.add('hidden');
            return false;
        }

        const response = await fetch("{{ route('usuarios.verificarUnico') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({ campo, valor })
        });

        const data = await response.json();

        if (data.existe) {
            errorElemento.textContent =
                campo === 'nombre_usuario'
                    ? 'Este nombre de usuario ya está registrado.'
                    : 'Este correo electrónico ya está registrado.';
            errorElemento.classList.remove('hidden');
            return true;
        } else {
            errorElemento.classList.add('hidden');
            return false;
        }
    }

    // Validar al salir de los campos
    inputNombre.addEventListener('blur', () => verificarCampo('nombre_usuario', inputNombre.value, errorNombre));
    inputCorreo.addEventListener('blur', () => verificarCampo('email', inputCorreo.value, errorCorreo));

    // Validar antes de enviar
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const nombreDuplicado = await verificarCampo('nombre_usuario', inputNombre.value, errorNombre);
        const correoDuplicado = await verificarCampo('email', inputCorreo.value, errorCorreo);

        if (nombreDuplicado || correoDuplicado) {
            alert('Por favor, corrige los errores antes de continuar.');
            return;
        }

        form.submit();
    });
});
</script>
