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

    <input type="hidden" name="idInstitucion" value="{{ $institucion->id }}">

    <div class="mb-4">
        <label class="block font-medium">Nombre del Departamento</label>
        <input type="text" name="departamento" id="departamento"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Encargado del Departamento</label>
        <input type="text" name="encargado_departamento" id="encargado_departamento"
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