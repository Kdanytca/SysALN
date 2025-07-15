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
        <input type="text" name="nombre_institucion" id="nombre_institucion"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Tipo de Institucion</label>
        <input type="text" name="tipo_institucion" id="tipo_institucion"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Proyecto</label>
        <input type="text" name="encargado_proyecto" id="encargado_proyecto"
            class="w-full border rounded px-3 py-2" required>
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