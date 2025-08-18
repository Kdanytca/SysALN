<!-- Validación de errores -->
@if ($errors->any())
<div class="mb-4">
    <ul class="list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Formulario -->
<form method="POST" action="{{ route('instituciones.store') }}">
    @csrf
    <div class="mb-4">
        <label class="block font-medium">Nombre</label>
        <input type="text" name="nombre_institucion"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Tipo de Institucion</label>
        <input type="text" name="tipo_institucion"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Proyecto</label>

        <select id="idEncargadoInstitucion" name="idEncargadoInstitucion" class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un encargado</option>
            @foreach ($usuariosParaCrear as $usuario)
                <option value="{{ $usuario->id }}">
                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                </option>
            @endforeach
        </select>

        <p class="text-sm text-gray-600 mt-2">
            ¿No encuentras al encargado?
            <button type="button"
                @click="modalNuevoUsuario = true"
                class="ml-2 text-blue-600 hover:underline">
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

