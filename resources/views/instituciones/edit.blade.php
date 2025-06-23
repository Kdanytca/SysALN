<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="nombre" id="nombre"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ old('nombre', $institucion->nombre ?? '') }}" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tipo</label>
        <input type="text" name="tipo" id="tipo"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ old('tipo', $institucion->tipo ?? '') }}" required>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700">Encargado</label>
        <input type="text" name="encargado" id="encargado"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
            value="{{ old('encargado', $institucion->encargado ?? '') }}" required>
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