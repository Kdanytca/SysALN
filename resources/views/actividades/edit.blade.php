<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <input type="hidden" name="idMetas" value="{{ $meta->id }}">

    <div class="mb-4">
        <label for="idUsuario" class="block text-sm font-medium text-gray-700"></label>
        <select name="idUsuario" id="idUsuario"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="$actividad->idUsuario">{{ $actividad->usuario->nombre_usuario }}</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}">{{ $usuario->nombre_usuario }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre Actividad</label>
        <input type="text" name="nombre_actividad" id="nombre_actividad" value="{{ $actividad->nombre_actividad }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Objetivos</label>
        <input type="text" name="objetivos" id="objetivos" value="{{ $actividad->objetivos }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $actividad->fecha_inicio }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $actividad->fecha_fin }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Resultados Esperados</label>
        <input type="text" name="resultados_esperados" id="resultados_esperados" value="{{ $actividad->resultados_esperados }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Unidad Encargada</label>
        <input type="text" name="unidad_encargada" id="unidad_encargada" value="{{ $actividad->unidad_encargada }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="editModalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Guardar
        </button>
    </div>
</form>