<form method="POST" action="{{ route('departamentos.store') }}">
    @if ($errors->any())
    <div class="mb-4">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @csrf

    <div class="mb-4">
        <label class="block font-medium">Institución</label>

        <select name="institucionVisible" class="w-full border rounded px-3 py-2" disabled>
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $inst)
                <option value="{{ $inst->id }}"
                    {{ $institucion->id == $inst->id ? 'selected' : '' }}>
                    {{ $inst->nombre_institucion }}
                </option>
            @endforeach
        </select>

        <!-- Campo oculto para enviar el ID real -->
        <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">
    </div>

    <div class="mb-4">
        <label class="block font-medium">Nombre del Departamento <i class="text-red-500">*</i></label>
        <input type="text" name="departamento"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Departamento <i class="text-red-500">*</i></label>

        <select name="idEncargadoDepartamento"
                class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaCrear as $usuario)
                <option value="{{ $usuario->id }}">{{ $usuario->nombre_usuario }} ({{ $usuario->email }})</option>
            @endforeach
        </select>

        <p class="text-sm text-gray-600 mt-2">
            ¿No encuentras al encargado?
            <button type="button"
                @click="modalNuevoUsuario = true"
                class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                Agregar nuevo usuario
            </button>
        </p>
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