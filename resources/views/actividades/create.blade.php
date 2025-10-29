<form id="formNuevaActividad" class="formActividad" method="POST" action="{{ route('actividades.store') }}" x-ref="formNuevaActividad" data-fecha-inicio-meta="{{ $meta->fecha_inicio }}" data-fecha-fin-meta="{{ $meta->fecha_fin }}" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="idMetas" value="{{ $meta->id ?? '' }}">

    <div x-data="actividadForm('nuevo', '{{ old('idEncargadoActividad', $actividad->idEncargadoActividad ?? '') }}', '{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}')" 
        x-init="init('nuevo')" class="space-y-4">

        {{-- Usuario Responsable --}}
        <div>
            <label class="block font-medium">Usuario Responsable<i class="text-red-500">*</i></label>
            <select name="idEncargadoActividad" id="idEncargadoActividad_nuevo" x-model="usuarioSeleccionado"
                @change="actualizarUnidad('nuevo')" class="w-full border rounded px-3 py-2" required>
                @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" data-departamento="{{ $usuario->departamento->departamento ?? '' }}">
                    {{ $usuario->nombre_usuario }} ({{ $usuario->email }})
                </option>
                @endforeach
            </select>

            <p class="text-sm text-gray-600 mt-2">
                ¿No encuentras al encargado?
                <button type="button" @click="modalNuevoUsuario = true" class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                    Agregar nuevo usuario
                </button>
            </p>
        </div>

        {{-- Nombre Actividad --}}
        <div class="mb-4">
            <label class="block font-medium">Nombre Actividad / Linea de Acción<i class="text-red-500">*</i></label>
            <select name="nombre_actividad" class="w-full border rounded px-3 py-2" required>
                <option value="">Seleccione una Actividad / Linea de Acción</option>
                @foreach($actividadesDisponibles->unique() as $actividadDisponible)
                    <option value="{{ $actividadDisponible }}">
                        {{ $actividadDisponible }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Objetivos --}}
        <div class="mb-4">
            <label class="block font-medium">Objetivos</label>
            <div id="contenedorObjetivos">
                <div class="input-con-x mb-2">
                    <input type="text" name="objetivos[]" class="border rounded px-3 py-2" placeholder="Opcional">
                    <button type="button" onclick="eliminarEsteCampo(this)">×</button>
                </div>
            </div>
            <div class="flex items-center gap-4 mt-2">
                <button type="button"
                    onclick="agregarCampo('contenedorObjetivos', 'objetivos[]', 'btnEliminarObjetivo')"
                    class="inline-flex items-center border border-gray-300 text-gray-700 text-xs font-medium px-2.5 py-1 rounded hover:bg-gray-50">
                    + Agregar otro objetivo
                </button>
            </div>
        </div>

        {{-- Fechas --}}
        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Inicio<i class="text-red-500">*</i></label>
                <input type="date" name="fecha_inicio" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Fin<i class="text-red-500">*</i></label>
                <input type="date" name="fecha_fin" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Evidencia (imágenes y documentos) --}}
        <div class="mb-4" x-data="previsualizacionEvidencia()" x-init="init()">
            <label class="block font-medium mb-2">Evidencia (imágenes o documentos)</label>
            <input 
                type="file" name="evidencia[]" multiple 
                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                @change="manejarArchivos($event)" 
                class="w-full border rounded px-3 py-2" x-ref="inputEvidencia">

            <p class="text-sm text-gray-600 mt-1">
                Puedes subir imágenes y documentos. Cada archivo debe ser menor a 5MB.
            </p>

            {{-- Previsualización --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <template x-for="(item, index) in nuevosArchivos" :key="'nuevo-' + index">
                    <div class="relative group">
                        <template x-if="item.tipo === 'imagen'">
                            <img :src="item.preview" @click="verArchivo(item.preview)" 
                                class="w-full h-32 object-cover rounded cursor-pointer hover:scale-105 transition" />
                        </template>
                        <template x-if="item.tipo === 'documento'">
                            <div class="bg-gray-200 p-4 rounded flex items-center justify-between">
                                <span class="text-xs font-medium truncate" x-text="item.nombre"></span>
                            </div>
                        </template>
                        <button type="button" @click="eliminarNuevo(index)"
                                class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded hover:opacity-100">
                            ✕
                        </button>
                    </div>
                </template>
            </div>

            <!-- Modal pantalla completa -->
            <div x-show="modalVisible" x-transition
                class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center"
                @keydown.escape.window="modalVisible = false">
                <div class="relative">
                    <button type="button" @click="modalVisible = false"
                            class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300 z-50">
                        &times;
                    </button>
                    <img :src="archivoActual" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg">
                    <a :href="archivoActual" download
                    class="absolute bottom-2 right-2 bg-white text-black px-3 py-1 rounded shadow hover:bg-gray-200">
                        Descargar
                    </a>
                </div>
            </div>
        </div>

        {{-- Comentario --}}
        <div class="mb-4">
            <label class="block font-medium">Comentario</label>
            <textarea name="comentario" class="w-full border rounded px-3 py-2" placeholder="Opcional">{{ old('comentario', $actividad->comentario ?? '') }}</textarea>
        </div>

        {{-- Unidad Encargada --}}
        <div class="mb-4">
            <label class="block font-medium mt-4">Unidad Encargada<i class="text-red-500">*</i></label>
            <select id="unidad_encargada_display_nuevo" class="w-full border rounded px-3 py-2">
                <option value="">Sin Departamento</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->departamento }}">{{ $departamento->departamento }}</option>
                @endforeach
            </select>
            <input type="hidden" name="unidad_encargada" id="unidad_encargada_nuevo" x-model="unidad">
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button type="button" @click="limpiarFormularioCrearActividad(); modalOpen = false"
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
            
            // Solo autocompleta si hay usuario seleccionado
            if(opcion && opcion.value) {
                this.unidad = opcion.getAttribute('data-departamento') ?? '';
            }

            const selectDepartamento = document.getElementById('unidad_encargada_display_' + sufijo);
            for (let option of selectDepartamento.options) {
                option.selected = option.value === this.unidad;
            }
        },

        // Actualiza la unidad si el usuario cambia manualmente
        unidadManual(sufijo = 'nuevo') {
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + sufijo);
            this.unidad = selectDepartamento.value;
        },

        init(sufijo = 'nuevo') {
            const selectUsuario = document.getElementById('idEncargadoActividad_' + sufijo);
            if (selectUsuario && this.usuarioSeleccionado) {
                selectUsuario.value = this.usuarioSeleccionado;
                this.actualizarUnidad(sufijo);
            }

            // Escucha cambios manuales en departamento
            const selectDepartamento = document.getElementById('unidad_encargada_display_' + sufijo);
            selectDepartamento.addEventListener('change', () => this.unidadManual(sufijo));
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