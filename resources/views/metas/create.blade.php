<form id="formNuevaMeta" x-ref="formNuevaMeta" method="POST" action="{{ route('metas.store') }}">
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

    
    <div class="mb-4">
        <label class="block font-medium">Plan Estrategico</label>
        <input type="text" disabled value="{{ $plan->nombre_plan_estrategico }}"
            class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700">
        <input type="hidden" name="idPlanEstrategico" value="{{ $plan->id }}">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Usuario Responsable</label>
        <select name="idEncargadoMeta" required class="w-full rounded-md border border-gray-500 shadow-sm">
            <option value="">Seleccione un usuario</option>
            @foreach ($usuarios as $usuario)
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

    <div class="mb-4">
        <label class="block font-medium">Nombre de la Meta</label>
        <input type="text" name="nombre_meta"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-2">Ejes Estratégicos</label>
        <div class="flex flex-wrap gap-2">
            @php
                $ejesSeleccionados = old('ejes_estrategicos', $meta->ejes_estrategicos ?? []);
                if (is_string($ejesSeleccionados)) {
                    $ejesSeleccionados = explode(',', $ejesSeleccionados);
                }
            @endphp

            @foreach (explode(',', $plan->ejes_estrategicos) as $eje)
                @php $eje = trim($eje); @endphp
                <label class="flex items-center space-x-2 text-sm bg-gray-100 px-2 py-1 rounded">
                    <input
                        type="checkbox"
                        name="ejes_estrategicos[]"
                        value="{{ $eje }}"
                        {{ in_array($eje, $ejesSeleccionados) ? 'checked' : '' }}
                    >
                    <span>{{ $eje }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Actividades</label>
        <div id="contenedorActividades">
            <input type="text" name="nombre_actividades[]" class="w-full border rounded px-3 py-2 mb-2" required>
        </div>
        <div class="flex items-center gap-4 mt-2">
            <button type="button"
                onclick="agregarActividad('contenedorActividades', 'btnEliminarActividad')"
                class="text-sm text-blue-600 underline hover:text-blue-800 transition">
                + Agregar otra actividad
            </button>

            <button type="button"
                onclick="eliminarUltimaActividad('contenedorActividades', 'btnEliminarActividad')"
                id="btnEliminarActividad"
                class="text-sm text-red-600 underline hover:text-red-800 transition hidden">
                🗑 Eliminar última actividad
            </button>
        </div>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Comentario</label>
        <textarea name="comentario"
            class="w-full border rounded px-3 py-2" required></textarea>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="modalOpen = false; limpiarFormularioCrear();"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Guardar
        </button>
    </div>
</form>
