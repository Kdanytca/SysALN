<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <!-- <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Institución</label>
        <select name="idInstitucion" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="{{ $departamento->idInstitucion}}">{{ $departamento->instituciones->nombre}}</option>
            @foreach ($instituciones as $institucion)
                <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
            @endforeach
        </select>
    </div> -->

    <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">
    
    <div class="mb-4">
        <label for="departamento" class="block text-sm font-medium text-gray-700">Nombre del Departamento</label>
        <input type="text" name="departamento" id="departamento"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
               value="{{ $departamento->departamento }}" required>
    </div>

    <div class="mb-4">
        <label for="encargado_departamento" class="block text-sm font-medium text-gray-700">Encargado del Departamento</label>
        <input type="text" name="encargado_departamento" id="encargado_departamento"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
               value="{{ $departamento->encargado_departamento }}" required>
    </div>

    <div class="flex justify-end">
        <button type="button"
                class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"
                @click="editModalOpen = false">
            Cancelar
        </button>
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Actualizar
        </button>
    </div>
</form>
