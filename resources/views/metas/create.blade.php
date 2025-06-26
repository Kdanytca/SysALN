<form method="POST" action="{{ route('metas.store') }}">
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

    <input type="hidden" name="idPlanEstrategico" value="{{ $plan->id }}">

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Plan Estrategico</label>
        <input type="text" disabled value="{{ $plan->nombre_plan_estrategico }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Usuario Responsable</label>
        <input type="text" name="usuario_responsable" id="ususario_responsable"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre de la Meta</label>
        <input type="text" name="nombre_meta" id="nombre_meta"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Ejes Estrategicos</label>
        <input type="text" name="ejes_estrategicos" id="ejes_estrategicos"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Actividades</label>
        <input type="text" name="actividades" id="actividades"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
        <input type="date" name="fecha_fin" id="fecha_fin"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Comentario</label>
        <textarea name="comentario" id="comentario"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"></textarea>
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