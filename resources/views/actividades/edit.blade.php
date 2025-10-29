<form id="formEditarActividad_{{ $actividad->id }}" class="formActividad" method="POST" action="{{ $action }}" data-fecha-inicio-meta="{{ $meta->fecha_inicio }}" data-fecha-fin-meta="{{ $meta->fecha_fin }}" enctype="multipart/form-data">
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
            <label for="idEncargadoActividad_{{ $actividad->id }}" class="block font-medium">Usuario Responsable<i class="text-red-500">*</i></label>
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
            <label class="block font-medium">Nombre Actividad / Linea de Acción<i class="text-red-500">*</i></label>
            <select name="nombre_actividad" class="w-full border rounded px-3 py-2" required>
                @if (!$actividad->nombre_actividad) 
                    <option value="">-- Selecciona una Actividad / Linea de Acción --</option>
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
                @foreach (json_decode($actividad->objetivos, true) as $objetivo)
                    <div class="input-con-x mb-2">
                        <input type="text" name="objetivos[]" value="{{ trim($objetivo) }}" class="border rounded px-3 py-2" placeholder="Opcional">
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
                <label class="block font-medium">Fecha de Inicio<i class="text-red-500">*</i></label>
                <input type="date" name="fecha_inicio" 
                    value="{{ old('fecha_inicio', $actividad->fecha_inicio) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>
        
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Fin<i class="text-red-500">*</i></label>
                <input type="date" name="fecha_fin" 
                    value="{{ old('fecha_fin', $actividad->fecha_fin) }}"
                    class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Evidencia actual (imágenes y documentos) --}}
        <div class="mb-4" 
            x-data="previsualizacionEvidencia({ 
                existentes: {{ json_encode(json_decode($actividad->evidencia ?? '[]')) }}, 
                formId: {{ $actividad->id }} 
            })" 
            x-init="init()">

            <label class="block font-medium mb-2">Evidencia actual</label>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2" x-show="archivosExistentes.length > 0">
                <template x-for="(archivo, index) in archivosExistentes" :key="'existente-' + index">
                    <div class="relative group">
                        <template x-if="archivo.match(/\.(jpg|jpeg|png|gif)$/)">
                            <img :src="'/' + archivo" @click="verArchivo('/' + archivo)"
                                class="w-full h-32 object-cover rounded cursor-pointer hover:scale-105 transition">
                        </template>
                        <template x-if="!archivo.match(/\.(jpg|jpeg|png|gif)$/)">
                            <div class="bg-gray-200 p-4 rounded flex items-center justify-between">
                                <a :href="'/' + archivo" target="_blank" class="text-xs font-medium underline">
                                    Ver documento
                                </a>
                            </div>
                        </template>
                        <button type="button" @click="eliminarExistente(index)"
                                class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded hover:opacity-100">
                            ✕
                        </button>
                    </div>
                </template>
            </div>

            {{-- Inputs ocultos para eliminar archivos --}}
            <div :id="'inputs-eliminar-' + formId"></div>

            <template x-if="archivosExistentes.length === 0 && archivosEliminados.length === 0">
                <p class="text-sm text-gray-500 mt-2">No hay evidencia agregada actualmente.</p>
            </template>

            {{-- Agregar nueva evidencia --}}
            <label class="block font-medium mt-4 mb-2">Agregar nueva evidencia</label>
            <input type="file" name="evidencia_nueva[]" multiple 
                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                @change="manejarArchivos($event)" class="w-full border rounded px-3 py-2" x-ref="inputEvidencia">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <template x-for="(item, index) in nuevosArchivos" :key="'nuevo-' + index">
                    <div class="relative group">
                        <template x-if="item.tipo === 'imagen'">
                            <img :src="item.preview" @click="verArchivo(item.preview)"
                                class="w-full h-32 object-cover rounded cursor-pointer hover:scale-105 transition">
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

            {{-- Modal pantalla completa --}}
            <div x-show="modalVisible" x-transition
                class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center"
                @click.away="modalVisible = false" 
                @keydown.escape.window="modalVisible = false">
                <div class="relative">
                    <img :src="archivoActual" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg">
                    <button type="button" @click="modalVisible = false"
                            class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300 z-50">
                        &times;
                    </button>
                    <a :href="archivoActual" download 
                    class="absolute bottom-2 right-2 bg-white text-black px-3 py-1 rounded shadow hover:bg-gray-200 z-50">
                        Descargar
                    </a>
                </div>
            </div>
        </div>

        {{-- Comentario --}}
        <div class="mb-4">
            <label class="block font-medium">Comentario</label>
            <textarea name="comentario" class="w-full border rounded px-3 py-2" placeholder="Opcional">{{ old('comentario', $actividad->comentario) }}</textarea>
        </div>

        {{-- Unidad Encargada --}}
        <div class="mb-4">
            <label class="block font-medium">Unidad Encargada<i class="text-red-500">*</i></label>
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
            if (selectDepartamento) {
                selectDepartamento.addEventListener('change', () => this.unidadManual(id));
            }
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