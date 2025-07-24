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
        <label class="block font-medium">Nombre del Departamento</label>
        <input type="text" name="departamento" id="departamento"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Departamento</label>
        <select name="idEncargadoDepartamento" id="idEncargadoDepartamento" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaCrear as $usuario)
                <option value="{{ $usuario->id }}">{{ $usuario->nombre_usuario }} ({{ $usuario->email }})</option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="modalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Guardar
        </button>
    </div>
</form>