<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <div class="mb-4">
        <label for="idInstitucion" class="block font-medium">Institución Perteneciente</label>

        <select name="institucionVisible" class="w-full border rounded px-3 py-2" disabled>
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $inst)
                <option value="{{ $inst->id }}" {{ $departamento->idInstitucion == $inst->id ? 'selected' : '' }}>
                    {{ $inst->nombre_institucion }}
                </option>
            @endforeach
        </select>

        <!-- Campo oculto para enviar el valor real -->
        <input type="hidden" name="idInstitucion" value="{{ $departamento->idInstitucion }}">
    </div>

    <div class="mb-4">
        <label for="departamento" class="block font-medium">Nombre del Departamento</label>
        <input type="text" name="departamento"
            class="w-full border rounded px-3 py-2"
            value="{{ $departamento->departamento }}" required>
    </div>

    <div class="mb-4">
        <label for="encargado_departamento" class="block font-medium">Encargado del
            Departamento</label>
        <select name="idEncargadoDepartamento" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaEditar[$departamento->id] ?? [] as $usuario)
                <option value="{{ $usuario->id }}"
                    {{ (isset($departamento) && $departamento->idEncargadoDepartamento == $usuario->id) ? 'selected' : '' }}>
                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                </option>
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
        <button type="button" class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
            @click="editModalOpen = false">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Actualizar
        </button>
    </div>
</form>