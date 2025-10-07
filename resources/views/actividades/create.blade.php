<form id="formNuevaActividad" class="formActividad" method="POST" action="{{ route('actividades.store') }}" x-ref="formNuevaActividad" data-fecha-inicio-meta="{{ $meta->fecha_inicio }}" data-fecha-fin-meta="{{ $meta->fecha_fin }}" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="idMetas" value="{{ $meta->id ?? '' }}">

    <div x-data="actividadForm('nuevo', '{{ old('idEncargadoActividad', $actividad->idEncargadoActividad ?? '') }}', '{{ old('unidad_encargada', $actividad->unidad_encargada ?? '') }}')" 
        x-init="init('nuevo')" class="space-y-4">

        {{-- Usuario Responsable --}}
        <div>
            <label class="block font-medium">Usuario Responsable</label>
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
            <label class="block font-medium">Nombre Actividad</label>
            <select name="nombre_actividad" class="w-full border rounded px-3 py-2" required>
                <option value="">Seleccione una actividad</option>
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
                    <input type="text" name="objetivos[]" class="border rounded px-3 py-2" required>
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
                <label class="block font-medium">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="w-1/2">
                <label class="block font-medium">Fecha de Fin</label>
                <input type="date" name="fecha_fin" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Imágenes --}}
        <div class="mb-4" x-data="previsualizacionImagenes({{ isset($actividad) ? json_encode(json_decode($actividad->imagenes ?? '[]')) : '{}' }})" x-init="init()">
            <label class="block font-medium mb-2">Imágenes</label>
            <input 
                type="file" name="imagenes[]" multiple accept="image/*" @change="manejarArchivos($event)" 
                class="w-full border rounded px-3 py-2" x-ref="inputImagenes">
            <p class="text-sm text-gray-600 mt-1">
                Puedes subir múltiples imágenes. Cada imagen debe ser menor a 4MB.
            </p>

            {{-- Previsualización --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <template x-for="(imagen, index) in nuevasImagenes" :key="'nueva-' + index">
                    <div class="relative group">
                        <img :src="imagen.preview" @click="verImagen(imagen.preview)" 
                            class="w-full h-32 object-cover rounded cursor-pointer hover:scale-105 transition">
                        <button type="button" @click="eliminarNueva(index)" 
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
                <!-- Botón cerrar -->
                <button type="button" @click="modalVisible = false"
                        class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300 z-50">
                    &times;
                </button>

                <!-- Imagen -->
                <img :src="imagenActual" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg">

                <!-- Botón descargar -->
                <a :href="imagenActual" download
                class="absolute bottom-2 right-2 bg-white text-black px-3 py-1 rounded shadow hover:bg-gray-200">
                    Descargar
                </a>
            </div>
        </div>

        </div>

        {{-- Comentario --}}
        <div class="mb-4">
            <label class="block font-medium">Comentario</label>
            <textarea name="comentario" class="w-full border rounded px-3 py-2" required>{{ old('comentario', $actividad->comentario ?? '') }}</textarea>
        </div>

        {{-- Unidad Encargada --}}
        <div class="mb-4">
            <label class="block font-medium mt-4">Unidad Encargada</label>
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