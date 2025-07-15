<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    <input type="hidden" name="idMetas" value="{{ old('idMetas', $actividad->idMetas) }}">

    {{-- USUARIO --}}
    <div class="mb-4">
        <label for="idUsuario" class="block font-medium">Usuario</label>
        <select name="idUsuario" id="idUsuario_{{ $actividad->id }}"
            class="w-full border rounded px-3 py-2" required>
            <option value="">Seleccione un Usuario</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}"
                    data-departamento="{{ $usuario->departamento->departamento ?? '' }}"
                    {{ old('idUsuario', $actividad->idUsuario) == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->nombre_usuario }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Nombre Actividad</label>
        <input type="text" name="nombre_actividad" id="nombre_actividad"
            value="{{ old('nombre_actividad', $actividad->nombre_actividad) }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Objetivos</label>
        <input type="text" name="objetivos" id="objetivos"
            value="{{ old('objetivos', $actividad->objetivos) }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio"
                value="{{ old('fecha_inicio', $actividad->fecha_inicio) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin"
                value="{{ old('fecha_fin', $actividad->fecha_fin) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Resultados Esperados</label>
        <input type="text" name="resultados_esperados" id="resultados_esperados"
            value="{{ old('resultados_esperados', $actividad->resultados_esperados) }}"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
    <label class="block font-medium">Unidad Encargada</label>
    <select id="unidad_encargada_display_{{ $actividad->id }}" disabled
        class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700">
        <option value="">Seleccione un departamento</option>
        @foreach($departamentos as $departamento)
            <option value="{{ $departamento->departamento }}"
                {{ old('unidad_encargada', $actividad->unidad_encargada ?? '') == $departamento->departamento ? 'selected' : '' }}>
                {{ $departamento->departamento }}
            </option>
        @endforeach
    </select>
    <!-- Campo oculto que se envía al backend -->
    <input type="hidden" name="unidad_encargada" id="unidad_encargada_{{ $actividad->id }}"
        value="{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const usuarioSelect = document.getElementById('idUsuario_{{ $actividad->id }}');
    const unidadEncargadaDisplay = document.getElementById('unidad_encargada_display_{{ $actividad->id }}');
    const unidadEncargadaHidden = document.getElementById('unidad_encargada_{{ $actividad->id }}');
    const form = usuarioSelect.closest('form');

    // Cambiar departamento al seleccionar usuario
    usuarioSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const departamento = selectedOption.getAttribute('data-departamento');

        if (departamento) {
            // Actualiza el select visible
            for (let opt of unidadEncargadaDisplay.options) {
                opt.selected = opt.value === departamento;
            }
            // Actualiza el input hidden
            unidadEncargadaHidden.value = departamento;
        }
    });

    // Asegurar valor al enviar el formulario
    form.addEventListener('submit', function () {
        const selected = usuarioSelect.options[usuarioSelect.selectedIndex];
        const departamento = selected.getAttribute('data-departamento');
        if (departamento) {
            unidadEncargadaHidden.value = departamento;
        }
    });
});
</script>