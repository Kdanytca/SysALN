<div>
    <h2 class="text-xl font-bold mb-4">Evidencias de la Actividad</h2>

    @php
        $archivos = json_decode($actividad->evidencia, true);
    @endphp

    @if (is_array($archivos) && count($archivos) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($archivos as $archivo)
                @php
                    $rutaArchivo = asset('uploads/actividades/' . $archivo);
                    $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                @endphp

                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-between h-64">
                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <!-- Imagen -->
                        <div class="flex justify-center items-center h-32 w-full bg-gray-100 rounded-md overflow-hidden mb-2">
                            <img src="{{ asset('uploads/actividades/imagenes/' . basename($archivo)) }}"
                                alt="Evidencia"
                                class="h-full w-full object-contain">
                        </div>
                        <p class="text-center text-sm text-gray-700 truncate w-full">{{ basename($archivo) }}</p>
                        <a href="{{ asset('uploads/actividades/imagenes/' . basename($archivo)) }}"
                        target="_blank" download
                        class="text-indigo-600 hover:underline text-sm font-medium mt-1">
                        Descargar Imagen
                        </a>

                    @elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar']))
                        <!-- Documento con icono según tipo -->
                        @php
                            $iconos = [
                                'pdf' => asset('icons/pdf.png'),
                                'doc' => asset('icons/word.png'),
                                'docx' => asset('icons/word.png'),
                                'xls' => asset('icons/excel.png'),
                                'xlsx' => asset('icons/excel.png'),
                                'ppt' => asset('icons/ppt.png'),
                                'pptx' => asset('icons/ppt.png'),
                                'zip' => asset('icons/zip.png'),
                                'rar' => asset('icons/zip.png'),
                            ];
                            $icono = $iconos[$extension] ?? asset('icons/file.png');
                        @endphp

                        <div class="flex justify-center items-center h-32 w-full bg-gray-100 rounded-md overflow-hidden mb-2">
                            <img src="{{ $icono }}" alt="{{ strtoupper($extension) }}"
                                class="w-16 h-16 object-contain">
                        </div>
                        <p class="text-center text-sm text-gray-700 truncate w-full">{{ basename($archivo) }}</p>
                        <a href="{{ asset('uploads/actividades/documentos/' . basename($archivo)) }}"
                        target="_blank"
                        class="text-indigo-600 hover:underline text-sm font-medium mt-1">
                        Ver / Descargar
                        </a>

                    @else
                        <!-- Tipo desconocido -->
                        <div class="flex justify-center items-center h-32 w-full bg-gray-100 rounded-md overflow-hidden mb-2">
                            <img src="{{ asset('icons/file.png') }}" alt="Archivo" class="w-12 h-12 opacity-70">
                        </div>
                        <p class="text-gray-600 text-sm italic truncate">{{ basename($archivo) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 italic text-sm">No hay evidencias registradas para esta actividad.</p>
    @endif

    <div class="mt-6 text-right">
        <button @click="modalEvidencia = false"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
            Cerrar
        </button>
    </div>
</div>
