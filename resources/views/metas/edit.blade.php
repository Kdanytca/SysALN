<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <input type="hidden" name="idPlanEstrategico" value="{{ $plan->id }}">

    <div class="mb-4">
        <label class="block font-medium">Plan Estrategico</label>
        <input type="text" disabled value="{{ $plan->nombre_plan_estrategico }}"
            class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700">
    </div>

    <div class="mb-4">
        <label class="block font-medium">Usuario Responsable</label>
        <select name="usuario_responsable"
            class="w-full border rounded px-3 py-2"
            required>
            <option value="" disabled>Seleccione un usuario</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->nombre_usuario }}"
                    {{ $usuario->nombre_usuario == $meta->usuario_responsable ? 'selected' : '' }}>
                    {{ $usuario->nombre_usuario }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Nombre de la Meta</label>
        <input type="text" name="nombre_meta" id="nombre_meta" value="{{ $meta->nombre_meta }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-2">Ejes Estratégicos</label>
        <div class="flex flex-wrap gap-2">
            @php
                // Ejes disponibles desde el plan
                $ejesPlan = explode(',', $plan->ejes_estrategicos);

                // Ejes seleccionados en la meta o por validación
                $ejesSeleccionados = old('ejes_estrategicos', $meta->ejes_estrategicos ?? []);

                // Convertir a array si es string
                if (is_string($ejesSeleccionados)) {
                    $ejesSeleccionados = explode(',', $ejesSeleccionados);
                }

                // Limpiar espacios de cada eje seleccionado
                $ejesSeleccionados = array_map('trim', $ejesSeleccionados);
            @endphp

            @foreach ($ejesPlan as $eje)
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
        <label class="block font-medium">Actividades</label>
        <div id="contenedorActividadesEdit">
            @foreach (explode(',', $meta->nombre_actividades) as $actividad)
                <input type="text"
                    name="nombre_actividades[]"
                    value="{{ trim($actividad) }}"
                    class="w-full border rounded px-3 py-2 mb-2"
                    required>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-2">
            <button type="button"
                onclick="agregarActividad('contenedorActividadesEdit', 'btnEliminarActividadEdit')"
                class="text-sm text-blue-600 underline hover:text-blue-800 transition">
                + Agregar otra actividad
            </button>

            <button type="button"
                onclick="eliminarUltimaActividad('contenedorActividadesEdit', 'btnEliminarActividadEdit')"
                id="btnEliminarActividadEdit"
                class="text-sm text-red-600 underline hover:text-red-800 transition {{ count(explode(',', $meta->nombre_actividades)) > 1 ? '' : 'hidden' }}">
                🗑 Eliminar última actividad
            </button>
        </div>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $meta->fecha_inicio }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $meta->fecha_fin }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Comentario</label>
        <textarea name="comentario" id="comentario"
            class="w-full border rounded px-3 py-2">{{ $meta->comentario }}</textarea>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="editModalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Guardar
        </button>
    </div>
</form>
