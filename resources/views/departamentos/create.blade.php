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
<form method="POST" action="{{ route('departamentos.store') }}">
    @csrf

    <!-- <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Institución</label>
        <select name="institucion_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $institucion)
                <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
            @endforeach
        </select>
    </div> -->

    <input type="hidden" name="institucion_id" value="{{ $institucion->id }}">

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre del Departamento</label>
        <input type="text" name="nombre" id="nombre"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="modalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Guardar
        </button>
    </div>
</form>