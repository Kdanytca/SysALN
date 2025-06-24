<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="nombre_institucion" id="nombre_institucion"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ $institucion->nombre_institucion }}" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tipo de Institucion</label>
        <input type="text" name="tipo_institucion" id="tipo_institucion"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ $institucion->tipo_institucion }}" required>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700">Encargado del Proyecto</label>
        <input type="text" name="encargado_proyecto" id="encargado_proyecto"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ $institucion->encargado_proyecto }}" required>
    </div>

    <div class="flex justify-end">
        <button type="button" class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
            @click="editModalOpen = false">
            Cancelar
        </button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>