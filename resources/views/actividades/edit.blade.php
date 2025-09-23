<form id="formEditarActividad" class="formActividad" method="POST" action="{{ $action }}">
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
                    class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                    Agregar nuevo usuario
                </button>
            </p>
        </div>

        {{-- Nombre Actividad --}}
        <div class="mb-4">
            <label class="block font-medium">Nombre Actividad</label>
            <select name="nombre_actividad" class="w-full border rounded px-3 py-2" required>
                @if (!$actividad->nombre_actividad) 
                    <option value="">-- Selecciona una actividad --</option>
                @else
                    <option value="{{ $actividad->nombre_actividad }}" selected>
                        {{ $actividad->nombre_actividad }}
                    </option>
                @endif

                @foreach ($actividadesDisponibles as $actividadDisponible)
                    <option value="{{ $actividadDisponible }}">
                        {{ $actividadDisponible }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Objetivos --}}
        <div class="mb-4">
            <label class="block font-medium">Objetivos</label>
            <div id="contenedorObjetivosEdit">
                @foreach (explode(',', $actividad->objetivos) as $objetivo)
                    <div class="input-con-x mb-2">
                        <input type="text" name="objetivos[]" value="{{ trim($objetivo) }}" class="border rounded px-3 py-2" required>
                        <button type="button" onclick="eliminarEsteCampo(this)">×</button>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-2">
                <button type="button"
                    onclick="agregarCampo('contenedorObjetivosEdit', 'objetivos[]', 'btnEliminarObjetivoEdit')"
                    class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                    + Agregar otro objetivo
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

        {{-- Comentario --}}
        <div class="mb-4">
            <label class="block font-medium">Comentario</label>
            <textarea name="comentario" class="w-full border rounded px-3 py-2" required>{{ old('comentario', $actividad->comentario) }}</textarea>
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

        // Autocompleta unidad si se selecciona un usuario
        actualizarUnidad(id) {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + id);
            const opcion = selectUsuario?.options[selectUsuario.selectedIndex];

            if (opcion && opcion.value) {
                this.unidad = opcion.getAttribute('data-departamento') ?? '';
            }

            // Actualiza select visible
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + id);
            for (let option of selectDepartamento.options) {
                option.selected = option.value === this.unidad;
            }
        },

        // Actualiza unidad cuando el usuario la modifica manualmente
        unidadManual(id) {
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + id);
            this.unidad = selectDepartamento.value;
        },

        // Inicializa el formulario con los valores existentes
        init(id) {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + id);
            if (selectUsuario && this.usuarioSeleccionado) {
                selectUsuario.value = this.usuarioSeleccionado;
                this.actualizarUnidad(id);
            }

            // Escucha cambios manuales en departamento
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + id);
            selectDepartamento.addEventListener('change', () => this.unidadManual(id));
        }
    }));
});
</script>

<style>
    .input-con-x {
        position: relative;
        display: inline-block;
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