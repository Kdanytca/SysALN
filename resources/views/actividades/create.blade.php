<form method="POST" action="{{ route('actividades.store') }}">
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

    <input type="hidden" name="idMetas" value="{{ $meta->id ?? '' }}">

    <div class="mb-4">
        <select name="idUsuario" id="idUsuario" required
            class="w-full border rounded px-3 py-2">
            <option value="">Seleccione una Usuario</option>
            @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}"
                    data-departamento="{{ $usuario->departamento->departamento ?? '' }}">
                    {{ $usuario->nombre_usuario }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Nombre Actividad</label>
        <input type="text" name="nombre_actividad" id="nombre_actividad"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Objetivos</label>
        <input type="text" name="objetivos" id="objetivos"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="flex gap-4 mb-4">
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio"
                class="w-full border rounded px-3 py-2" required>
        </div>
    
        <div class="w-1/2">
            <label class="block font-medium">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin"
                class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Resultados Esperados</label>
        <input type="text" name="resultados_esperados" id="resultados_esperados"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Unidad Encargada</label>
        <select id="unidad_encargada_display" disabled
            class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700">
            <option value="">Seleccione un departamento</option>
            @foreach($departamentos as $departamento)
                <option value="{{ $departamento->departamento }}"
                    {{ old('unidad_encargada', $actividad->unidad_encargada ?? '') == $departamento->departamento ? 'selected' : '' }}>
                    {{ $departamento->departamento }}
                </option>
            @endforeach
        </select>
        <!-- Campo oculto para enviar el valor al backend -->
        <input type="hidden" name="unidad_encargada" id="unidad_encargada"
            value="{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}">
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

<script>
    document.getElementById('idUsuario').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const departamento = selectedOption.getAttribute('data-departamento');

        if (departamento) {
            // Mostrar visualmente
            const selectDepartamento = document.getElementById('unidad_encargada_display');
            for (let option of selectDepartamento.options) {
                option.selected = option.value === departamento;
            }

            // Enviar valor oculto
            document.getElementById('unidad_encargada').value = departamento;
        }
    });
</script>
