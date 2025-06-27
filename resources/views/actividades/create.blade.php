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

    <input type="hidden" name="idMetas" value="{{ $meta->id }}">

    <div class="mb-4">
        <select name="idUsuario" id="idUsuario" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
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
        <label class="block text-sm font-medium text-gray-700">Nombre Actividad</label>
        <input type="text" name="nombre_actividad" id="nombre_actividad"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Objetivos</label>
        <input type="text" name="objetivos" id="objetivos"
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
        <label class="block text-sm font-medium text-gray-700">Resultados Esperados</label>
        <input type="text" name="resultados_esperados" id="resultados_esperados"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Unidad Encargada</label>
        <select id="unidad_encargada_display" disabled
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
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
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
