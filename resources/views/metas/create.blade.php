<form id="formNuevaMeta" class="formMeta" x-ref="formNuevaMeta" method="POST" action="{{ route('metas.store') }}" data-fecha-inicio-plan="{{ $plan->fecha_inicio }}" data-fecha-fin-plan="{{ $plan->fecha_fin }}" x-data="{ tipo: 'meta' }">
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
        <input type="hidden" name="idPlanEstrategico" value="{{ $plan->id }}">
        <input type="hidden" name="tipo" :value="tipo">
    </div>

    <div class="mb-4">
        <label class="block font-medium">Usuario Responsable<i class="text-red-500">*</i></label>
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
                class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                Agregar nuevo usuario
            </button>
        </p>
    </div>

    {{-- Selector de tipo --}}
    <div class="mb-4">
        <label class="block font-medium mb-2">Tipo de Registro<i class="text-red-500">*</i></label>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2">
                <input type="radio" name="tipo_radio" value="meta" x-model="tipo">
                <span>Meta</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="tipo_radio" value="estrategia" x-model="tipo">
                <span>Estrategia</span>
            </label>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium" x-text="tipo === 'meta' ? 'Nombre de la Meta' : 'Nombre de la Estrategia'"></label>
        <input type="text" name="nombre" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Objetivos de la Estrategia</label>
        <div id="contenedorObjetivos">
            <div class="input-con-x mb-2">
                <input type="text" name="objetivos_estrategias[]" class="border rounded px-3 py-2">
                <button type="button" onclick="eliminarEsteCampo(this)">×</button>
            </div>
        </div>
        <div class="flex items-center gap-4 mt-2">
            <button type="button"
                onclick="agregarCampo('contenedorObjetivos', 'objetivos_estrategias[]')" 
                class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                + Agregar otro objetivo
            </button>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-2">Ejes Estratégicos<i class="text-red-500"> (Minimo 1)</i></label>
        <div class="flex flex-wrap gap-2">
            @php
                // Ejes seleccionados previamente, si existen en old() o en el modelo
                $ejesSeleccionados = old('ejes_estrategicos', []); // No necesitamos json_decode aquí ya que no estamos editando

                // Limpiar espacios de cada eje seleccionado
                $ejesSeleccionados = array_map('trim', $ejesSeleccionados);
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
        <label class="block font-medium">Actividades / Lineas de Acción<i class="text-red-500">*</i></label>
        <div id="contenedorActividades">
            <div class="input-con-x mb-2">
                <input type="text" name="nombre_actividades[]" class="border rounded px-3 py-2" required>
                <button type="button" onclick="eliminarEsteCampo(this)">×</button>
            </div>
        </div>
        <div class="flex items-center gap-4 mt-2">
            <button type="button"
                onclick="agregarCampo('contenedorActividades', 'nombre_actividades[]')"
                class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                + Agregar otra actividad
            </button>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Resultados Esperados</label>
        <input type="text" name="resultados_esperados" placeholder="Opcional"
            class="w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block font-medium">Indicador de Resultados<i class="text-red-500">*</i></label>
        <input type="text" name="indicador_resultados"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio<i class="text-red-500">*</i></label>
            <input type="date" name="fecha_inicio"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin<i class="text-red-500">*</i></label>
            <input type="date" name="fecha_fin"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Comentario</label>
        <textarea name="comentario" placeholder="Opcional"
            class="w-full border rounded px-3 py-2"></textarea>
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
