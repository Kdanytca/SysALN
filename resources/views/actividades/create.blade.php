<form method="POST"
    action="{{ $actividad->id ?? false ? route('actividades.update', $actividad) : route('actividades.store') }}">
    @csrf
    @if($actividad->id ?? false)
    @method('PUT')
    @endif

    <input type="hidden" name="idMetas" value="{{ $meta->id ?? '' }}">

    <div x-data="actividadForm('nuevo', '{{ old('idEncargadoActividad', $actividad->idEncargadoActividad ?? '') }}', '{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}')" 
        x-init="init('nuevo')" class="space-y-4">

        {{-- Usuario Responsable --}}
        <div>
            <label class="block font-medium">Usuario Responsable</label>
            <select name="idEncargadoActividad" id="idEncargadoActividad_nuevo" x-model="usuarioSeleccionado"
                @change="actualizarUnidad('nuevo')" class="w-full border rounded px-3 py-2" required>
                <option value="">Seleccione un usuario</option>
                @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" data-departamento="{{ $usuario->departamento->departamento ?? '' }}">
                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                </option>
                @endforeach
            </select>

            <p class="text-sm text-gray-600 mt-2">
                ¿No encuentras al encargado?
                <button type="button" @click="modalNuevoUsuario = true" class="ml-2 text-blue-600 hover:underline">
                    Agregar nuevo usuario
                </button>
            </p>
        </div>

        {{-- Nombre Actividad --}}
        <div>
            <label class="block font-medium">Nombre Actividad</label>
            <input type="text" name="nombre_actividad" class="w-full border rounded px-3 py-2"
                value="{{ old('nombre_actividad', $actividad->nombre_actividad ?? '') }}" required>
        </div>

        {{-- Objetivos --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Objetivos</label>
            <div id="contenedorObjetivos">
                <input type="text" name="objetivos[]" class="w-full border rounded px-3 py-2 mb-2" required>
            </div>
            <div class="flex items-center gap-4 mt-2">
                <button type="button"
                    onclick="agregarCampo('contenedorObjetivos', 'objetivos[]', 'btnEliminarObjetivo')"
                    class="text-sm text-blue-600 underline hover:text-blue-800 transition">
                    + Agregar otro objetivo
                </button>

                <button type="button" 
                    onclick="eliminarUltimoCampo('contenedorObjetivos', 'btnEliminarObjetivo')"
                    id="btnEliminarObjetivo"
                    class="text-sm text-red-600 underline hover:text-red-800 transition hidden">
                    🗑 Eliminar último objetivo
                </button>
            </div>
        </div>

        {{-- Fechas --}}
        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" class="w-full border rounded px-3 py-2"
                    value="{{ old('fecha_inicio', $actividad->fecha_inicio ?? '') }}" required>
            </div>
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Fin</label>
                <input type="date" name="fecha_fin" class="w-full border rounded px-3 py-2"
                    value="{{ old('fecha_fin', $actividad->fecha_fin ?? '') }}" required>
            </div>
        </div>

        {{-- Resultados esperados --}}
        <div>
            <label class="block font-medium">Resultados Esperados</label>
            <input type="text" name="resultados_esperados" class="w-full border rounded px-3 py-2"
                value="{{ old('resultados_esperados', $actividad->resultados_esperados ?? '') }}" required>
        </div>

        {{-- Unidad Encargada --}}
        <div>
            <label class="block font-medium mt-4">Unidad Encargada</label>
            <select id="unidad_encargada_display_nuevo"
                class="w-full border rounded px-3 py-2">
                <option value="">Sin Departamento</option>
                @foreach($departamentos as $departamento)
                <option value="{{ $departamento->departamento }}">{{ $departamento->departamento }}</option>
                @endforeach
            </select>
            <input type="hidden" name="unidad_encargada" id="unidad_encargada_nuevo" x-model="unidad">
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button type="button" @click="modalOpen = false"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Guardar
            </button>
        </div>

    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('actividadForm', (id = 'nuevo', usuarioInicial = '', unidadInicial = '') => ({
        usuarioSeleccionado: usuarioInicial,
        unidad: unidadInicial,

        actualizarUnidad(sufijo = 'nuevo') {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + sufijo);
            const opcion = selectUsuario?.options[selectUsuario.selectedIndex];
            this.unidad = opcion?.getAttribute('data-departamento') ?? '';

            const selectDepartamento = document.getElementById('unidad_encargada_display_' + sufijo);
            for (let option of selectDepartamento.options) {
                option.selected = option.value === this.unidad;
            }
        },

        init(sufijo = 'nuevo') {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + sufijo);
            if (selectUsuario && this.usuarioSeleccionado) {
                selectUsuario.value = this.usuarioSeleccionado;
                this.actualizarUnidad(sufijo);
            }
        }
    }));
});


</script>