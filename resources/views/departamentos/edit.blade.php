<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">

    <div class="mb-4">
        <label for="departamento" class="block font-medium">Nombre del Departamento</label>
        <input type="text" name="departamento" id="departamento"
            class="w-full border rounded px-3 py-2"
            value="{{ $departamento->departamento }}" required>
    </div>

    <div class="mb-4">
        <label for="encargado_departamento" class="block font-medium">Encargado del
            Departamento</label>
        <input type="text" name="encargado_departamento" id="encargado_departamento"
            class="w-full border rounded px-3 py-2"
            value="{{ $departamento->encargado_departamento }}" required>
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