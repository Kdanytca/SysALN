<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <div class="mb-4">
        <label for="idInstitucion" class="block font-medium">Institución Perteneciente</label>
        <select name="idInstitucion" id="idInstitucion" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $institucion)
            <option value="{{ $institucion->id }}" {{ (isset($departamento) && $departamento->idInstitucion == $institucion->id) ? 'selected' : '' }}>
                {{ $institucion->nombre_institucion }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="departamento" class="block font-medium">Nombre del Departamento</label>
        <input type="text" name="departamento" id="departamento"
            class="w-full border rounded px-3 py-2"
            value="{{ $departamento->departamento }}" required>
    </div>

    <div class="mb-4">
        <label for="encargado_departamento" class="block font-medium">Encargado del
            Departamento</label>
        <select name="idEncargadoDepartamento" id="idEncargadoDepartamento" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaEditar as $usuario)
            <option value="{{ $usuario->id }}"
                {{ (isset($departamento) && $departamento->idEncargadoDepartamento == $usuario->id) ? 'selected' : '' }}>
                {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
            </option>
            @endforeach
        </select>
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