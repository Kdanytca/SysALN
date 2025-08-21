<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <input type="hidden" name="idMetas" value="{{ old('idMetas', $actividad->idMetas) }}">

    <div 
        x-data="actividadForm('{{ old('idEncargadoActividad', $actividad->idEncargadoActividad) }}', '{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}')" 
        x-init="init('{{ $actividad->id }}')"
        class="space-y-4">

        {{-- Usuario Responsable --}}
        <div class="mb-4">
            <label for="idEncargadoActividad_{{ $actividad->id }}" class="block font-medium">Usuario Responsable</label>
            <select name="idEncargadoActividad" id="idEncargadoActividad_{{ $actividad->id }}" 
                class="w-full border rounded px-3 py-2" 
                x-model="usuarioSeleccionado"
                @change="actualizarUnidad('{{ $actividad->id }}')"
                required>
                <option value="">Seleccione un Usuario</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" 
                        data-departamento="{{ $usuario->departamento->departamento ?? '' }}">
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

        {{-- Nombre Actividad --}}
        <div class="mb-4">
            <label class="block font-medium">Nombre Actividad</label>
            <input type="text" name="nombre_actividad" 
                value="{{ old('nombre_actividad', $actividad->nombre_actividad) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- Objetivos --}}
        <div class="mb-4">
            <label class="block font-medium">Objetivos</label>
            <div id="contenedorObjetivosEdit">
                @foreach (explode(',', $actividad->objetivos) as $objetivo)
                    <input type="text" name="objetivos[]" value="{{ trim($objetivo) }}"
                        class="w-full border rounded px-3 py-2 mb-2" required>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-2">
                <button type="button"
                    onclick="agregarCampo('contenedorObjetivosEdit', 'objetivos[]', 'btnEliminarObjetivoEdit')"
                    class="text-sm text-blue-600 underline hover:text-blue-800 transition">
                    + Agregar otro objetivo
                </button>

                <button type="button"
                    onclick="eliminarUltimoCampo('contenedorObjetivosEdit', 'btnEliminarObjetivoEdit')"
                    id="btnEliminarObjetivoEdit"
                    class="text-sm text-red-600 underline hover:text-red-800 transition {{ count(explode(',', $meta->objetivos)) > 1 ? '' : 'hidden' }}">
                    🗑 Eliminar último objetivo
                </button>
            </div>
        </div>

        {{-- Fechas --}}
        <div class="flex gap-4 mb-4">
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" 
                    value="{{ old('fecha_inicio', $actividad->fecha_inicio) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>
        
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Fin</label>
                <input type="date" name="fecha_fin" 
                    value="{{ old('fecha_fin', $actividad->fecha_fin) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Resultados Esperados --}}
        <div class="mb-4">
            <label class="block font-medium">Resultados Esperados</label>
            <input type="text" name="resultados_esperados" 
                value="{{ old('resultados_esperados', $actividad->resultados_esperados) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- Unidad Encargada --}}
        <div class="mb-4">
            <label class="block font-medium">Unidad Encargada</label>
            <select id="unidad_encargada_display_{{ $actividad->id }}"
                class="w-full border rounded px-3 py-2">
                <option value="">Sin Departamento</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->departamento }}">{{ $departamento->departamento }}</option>
                @endforeach
            </select>
            <input type="hidden" name="unidad_encargada" id="unidad_encargada_{{ $actividad->id }}" x-model="unidad">
        </div>

        {{-- Botones --}}
        <div class="flex justify-end">
            <button type="button" @click="editModalOpen = false"
                class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Cancelar
            </button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Guardar
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('actividadForm', (usuarioInicial, unidadInicial) => ({
        usuarioSeleccionado: usuarioInicial,
        unidad: unidadInicial,

        actualizarUnidad(id) {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + id);
            const opcion = selectUsuario.options[selectUsuario.selectedIndex];
            this.unidad = opcion ? (opcion.getAttribute('data-departamento') ?? '') : '';

            // actualizar select visible
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + id);
            for (let option of selectDepartamento.options) {
                option.selected = option.value === this.unidad;
            }
        },

        init(id) {
            if (this.usuarioSeleccionado) {
                const selectUsuario = document.getElementById('idEncargadoActividad_' + id);
                if (!selectUsuario) return; // 👈 evita el error
                selectUsuario.value = this.usuarioSeleccionado;
                this.actualizarUnidad(id);
            }
        }
    }));
});
</script>
