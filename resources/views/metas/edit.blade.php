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
        <select name="idEncargadoMeta" class="w-full border rounded px-3 py-2" required>
            <option value="" disabled>Seleccione un usuario</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}"
                    {{ $usuario->id == $meta->idEncargadoMeta ? 'selected' : '' }}>
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
        <input type="text" name="nombre_meta" value="{{ $meta->nombre_meta }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-2">Ejes Estratégicos</label>
        <div class="flex flex-wrap gap-2">
            @php
                // Ejes disponibles desde el plan (string con comas)
                $ejesPlan = array_map('trim', explode(',', $plan->ejes_estrategicos));

                // Ejes seleccionados de old() o de la meta (decodificados de JSON)
                $ejesSeleccionados = old('ejes_estrategicos', []);

                if (empty($ejesSeleccionados)) {
                    // Si no hay valores de old(), usamos los ejes de la base de datos (como JSON)
                    $ejesSeleccionados = json_decode($meta->ejes_estrategicos, true) ?? [];
                }

                // Asegurarnos de que los ejes seleccionados estén limpios (array sin espacios)
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
                <div class="input-con-x mb-2">
                    <input type="text" name="nombre_actividades[]" value="{{ trim($actividad) }}"
                        class="border rounded px-3 py-2 w-full" required>
                    <button type="button" onclick="eliminarEsteCampo(this)">×</button>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-2">
            <button type="button"
                onclick="agregarActividad('contenedorActividadesEdit')"
                class="text-sm text-blue-600 underline hover:text-blue-800 transition">
                + Agregar otra actividad
            </button>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Resultados</label>
        <input type="text" name="resultados_esperados" value="{{ $meta->resultados_esperados }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Indicador</label>
        <input type="text" name="indicador_resultados" value="{{ $meta->indicador_resultados }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" value="{{ $meta->fecha_inicio }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin" value="{{ $meta->fecha_fin }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Comentario</label>
        <textarea name="comentario"
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

<style>
    .input-con-x {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-con-x input {
        width: 100%;
        padding-right: 2rem; /* espacio para la 'x' */
    }

    .input-con-x button {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: red;
        font-size: 1rem;
        cursor: pointer;
        line-height: 1;
    }
</style>
