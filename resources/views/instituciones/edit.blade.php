<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label class="block font-medium">Nombre</label>
        <input type="text" name="nombre_institucion"
            class="w-full border rounded px-3 py-2"
            value="{{ $institucion->nombre_institucion }}" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Tipo de Institucion</label>
        <input type="text" name="tipo_institucion"
            class="w-full border rounded px-3 py-2"
            value="{{ $institucion->tipo_institucion }}" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Proyecto</label>
        <select name="idEncargadoInstitucion" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaEditar[$institucion->id] as $usuario)
                <option value="{{ $usuario->id }}"
                    {{ $institucion->idEncargadoInstitucion == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                </option>
            @endforeach
        </select>

        <!-- Botón que usa el mismo modalNuevoUsuario definido en index -->
        <button type="button" @click="modalNuevoUsuario = true"
            class="mt-2 text-blue-600 text-sm hover:underline">
            Agregar nuevo usuario
        </button>
    </div>

    <div class="flex justify-end">
        <button type="button" class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
            @click="editModalOpen = false">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>

