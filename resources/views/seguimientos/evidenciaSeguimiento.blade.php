<div>
    <h2 class="text-xl font-bold mb-4">Evidencias del Seguimiento</h2>

    <form id="form-evidencias-seg-{{ $seguimiento->id }}" action="{{ route('seguimientos.guardarEvidencias', $seguimiento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        @php
            $archivos = json_decode($seguimiento->evidencia, true) ?? [];
        @endphp

        <div id="evidencias-grid-seg-{{ $seguimiento->id }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($archivos as $archivo)
                @php
                    $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                    $nombreArchivo = basename($archivo);
                    $isImage = in_array($extension, ['jpg','jpeg','png','gif','webp']);
                    $isDoc = in_array($extension, ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar']);
                @endphp
                <div class="evidencia-card-seg bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-between h-64 relative"
                    data-file="{{ $archivo }}">
                    <button type="button"
                        class="delete-existing-seg absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center">
                        ×
                    </button>
                    {{-- Mostrar imagen o documento --}}
                    @if ($isImage)
                        <div class="flex justify-center items-center h-32 w-full bg-gray-100 rounded-md overflow-hidden mb-2">
                            <img src="{{ asset($archivo) }}" alt="Evidencia" class="h-full w-full object-contain">
                        </div>
                        <p class="text-center text-sm text-gray-700 truncate w-full">{{ $nombreArchivo }}</p>
                        <a href="{{ asset($archivo) }}" target="_blank"
                            class="text-indigo-600 hover:underline text-sm font-medium mt-1">Ver / Descargar</a>
                    @elseif ($isDoc)
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
                            <img src="{{ $icono }}" class="w-16 h-16 object-contain">
                        </div>
                        <p class="text-center text-sm text-gray-700 truncate w-full">{{ $nombreArchivo }}</p>
                        <a href="{{ asset($archivo) }}" target="_blank"
                            class="text-indigo-600 hover:underline text-sm font-medium mt-1">Ver / Descargar</a>
                    @else
                        <div class="flex justify-center items-center h-32 w-full bg-gray-100 rounded-md overflow-hidden mb-2">
                            <img src="{{ asset('icons/file.png') }}" alt="Archivo" class="w-12 h-12 opacity-70">
                        </div>
                        <p class="text-gray-600 text-sm italic truncate">{{ $nombreArchivo }}</p>
                    @endif
                </div>
            @endforeach

            {{-- Tarjeta agregar --}}
            <div id="agregar-tarjeta-seg-{{ $seguimiento->id }}"
                class="bg-gray-100 p-4 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center h-64 hover:bg-gray-200 transition cursor-pointer">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-500 mx-auto mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm text-gray-600">Agregar evidencia</span>
                </div>
                <input id="file-picker-seg-{{ $seguimiento->id }}" type="file" multiple class="hidden"
                    accept=".jpeg,.jpg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                <div id="preview-new-seg-{{ $seguimiento->id }}" class="mt-3 grid grid-cols-3 gap-2 w-full"></div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <button type="button" @click="modalEvidenciaSeg = false"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Cerrar</button>
            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Guardar
                cambios</button>
        </div>
    </form>
</div>