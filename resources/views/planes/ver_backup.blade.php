<x-app-layout>
    @php
        // Función segura para decodificar valores
        function decodeSafe($value)
        {
            if (is_null($value)) {
                return 'N/A';
            }

            if (is_array($value) || is_object($value)) {
                return implode(', ', (array) $value);
            }

            if (
                is_string($value) &&
                (str_starts_with($value, '{') || str_starts_with($value, '[') || str_contains($value, '\u'))
            ) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return is_array($decoded) ? implode(', ', $decoded) : $decoded;
                }
            }

            return $value;
        }

        // Función segura para enumerar texto
        function formatearTextoEnumerado($texto)
        {
            if (empty($texto)) {
                return '<p class="italic text-gray-500">Sin información disponible</p>';
            }

            if (is_string($texto)) {
                $decoded = json_decode($texto, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = $decoded;
                } else {
                    $textoPlano = trim($texto, "[]\"' \n\r\t");
                    $textoPlano = str_replace(['•', "\r"], "\n", $textoPlano);
                    $items = preg_split("/[\n;]+/", $textoPlano);
                }
            } else {
                $items = (array) $texto;
            }

            $items = array_filter(array_map('trim', $items), fn($v) => $v !== '');
            if (empty($items)) {
                return '<p class="italic text-gray-500">Sin información disponible</p>';
            }

            $html = '';
            foreach ($items as $index => $item) {
                $html .= "<p class='mb-2'><strong>" . ($index + 1) . '.</strong> ' . e($item) . '</p>';
            }

            return $html;
        }

        // Decodificar metas del backup
        $metasBackup = $backup->metas;
        if (is_string($metasBackup)) {
            $metasBackup = json_decode($metasBackup, true) ?? [];
        }
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800">
            📑 Respaldo del Plan:
            <span class="text-indigo-600">{{ decodeSafe($backup->nombre_plan_estrategico) }}</span>
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">

        <!-- Información General -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 mb-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">📋 Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-base text-gray-700">
                <p><span class="font-bold text-gray-900">📂 Departamento:</span>
                    {{ decodeSafe($backup->nombre_departamento) }}</p>
                <p><span class="font-bold text-gray-900">👤 Responsable:</span>
                    {{ decodeSafe($backup->nombre_responsable) }}</p>
                <p><span class="font-bold text-gray-900">📅 Fecha Inicio:</span> {{ $backup->fecha_inicio }}</p>
                <p><span class="font-bold text-gray-900">📅 Fecha Fin:</span> {{ $backup->fecha_fin }}</p>
            </div>
        </div>

        <!-- Ejes y Objetivos -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-3">🎯 Ejes Estratégicos del Plan</h3>
                <div class="text-gray-700 leading-relaxed text-base break-words text-justify">
                    {!! formatearTextoEnumerado($backup->ejes_estrategicos) !!}
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-3">📌 Objetivos</h3>
                <div class="text-gray-700 leading-relaxed text-base break-words text-justify">
                    {!! formatearTextoEnumerado($backup->objetivos) !!}
                </div>
            </div>
        </div>

        <!-- Metas -->
        @if (!empty($metasBackup))
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">🎯 Metas / Objetivos Estratégicos</h3>

                @foreach ($metasBackup as $index => $meta)
                    @php
                        $actividades = $meta['actividades'] ?? [];
                        if (is_string($actividades)) {
                            $actividades = json_decode($actividades, true) ?? [];
                        }

                        $ejesMeta = $meta['ejes_estrategicos'] ?? [];
                        if (is_string($ejesMeta)) {
                            $ejesMeta = json_decode($ejesMeta, true) ?? [];
                        }
                    @endphp

                    <div x-data="{ open: false }" class="border border-blue-300 rounded-lg bg-blue-50 shadow-sm">
                        <button @click="open = !open"
                            class="w-full px-4 py-3 text-left font-semibold text-blue-800 hover:bg-blue-100 rounded-t-lg">
                            Meta {{ $index + 1 }}: {{ decodeSafe($meta['nombre'] ?? '') }}
                            @if (!empty($meta['responsable']))
                                (Encargado: {{ decodeSafe($meta['responsable']) }})
                            @endif
                        </button>

                        <div x-show="open" class="px-4 py-3 space-y-2">
                            <p><strong>Ejes Estratégicos:</strong></p>
                            <div class="ml-4">{!! formatearTextoEnumerado($ejesMeta) !!}</div>

                            <p><strong>Resultados Esperados:</strong>
                                {{ decodeSafe($meta['resultados_esperados'] ?? 'N/A') }}</p>
                            <p><strong>Indicador:</strong> {{ decodeSafe($meta['indicador_resultados'] ?? 'N/A') }}</p>
                            <p><strong>Comentario:</strong> {{ decodeSafe($meta['comentario'] ?? 'N/A') }}</p>
                            <p><strong>Periodo:</strong> {{ $meta['fecha_inicio'] ?? '' }} -
                                {{ $meta['fecha_fin'] ?? '' }}</p>

                            <!-- Actividades -->
                            @if (!empty($actividades))
                                <div class="ml-4 mt-2 space-y-2">
                                    @foreach ($actividades as $actIndex => $actividad)
                                        @php
                                            $seguimientos = $actividad['seguimientos'] ?? [];
                                            if (is_string($seguimientos)) {
                                                $seguimientos = json_decode($seguimientos, true) ?? [];
                                            }

                                            // Detectar si la actividad tenía OBJETIVOS o INDICADORES al hacer el backup
                                            $tipoCampo = $actividad['tipo_campo'] ?? 'objetivos'; // valor por defecto
                                            $lista = $actividad['contenido_campo'] ?? [];

                                            // Asegurar formato array
                                            if (is_string($lista)) {
                                                $lista = json_decode($lista, true) ?? [];
                                            }

                                            // Asegurar formato array
                                            if (is_string($lista)) {
                                                $lista = json_decode($lista, true) ?? [];
                                            }

                                            $evidencias = $actividad['evidencias'] ?? ($actividad['evidencia'] ?? []);
                                            if (is_string($evidencias)) {
                                                $evidencias = json_decode($evidencias, true) ?? [];
                                            }
                                        @endphp

                                        <div x-data="{ open: false }"
                                            class="border border-green-300 rounded-lg bg-green-50 shadow-sm">
                                            <button @click="open = !open"
                                                class="w-full px-3 py-2 text-left font-medium text-green-800 hover:bg-green-100 rounded-t-lg">
                                                Actividad {{ $actIndex + 1 }}:
                                                {{ decodeSafe($actividad['nombre_actividad'] ?? '') }}
                                                @if (!empty($actividad['encargado']))
                                                    (Encargado: {{ decodeSafe($actividad['encargado']) }})
                                                @endif
                                            </button>

                                            <div x-show="open" class="px-3 py-2 space-y-1">
                                                
                                                <p><strong>{{ ucfirst($tipoCampo) }}:</strong></p>
                                                <div class="ml-4">{!! formatearTextoEnumerado($lista) !!}</div>

                                                <p><strong>Comentario:</strong>
                                                    {{ decodeSafe($actividad['comentario'] ?? 'N/A') }}</p>
                                                <p><strong>Unidad Encargada:</strong>
                                                    {{ decodeSafe($actividad['unidad_encargada'] ?? 'N/A') }}</p>
                                                <p><strong>Periodo:</strong> {{ $actividad['fecha_inicio'] ?? '' }} -
                                                    {{ $actividad['fecha_fin'] ?? '' }}</p>

                                                <!-- Evidencias -->
                                                @if (!empty($evidencias))
                                                    <div class="mt-2">
                                                        <strong>📎 Evidencias:</strong>
                                                        <div class="flex flex-wrap gap-2 mt-1">
                                                            @foreach ($evidencias as $archivo)
                                                                @php $extension = pathinfo($archivo, PATHINFO_EXTENSION); @endphp
                                                                @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                    <img src="{{ asset($archivo) }}" alt="Evidencia"
                                                                        class="w-32 h-32 object-cover rounded shadow">
                                                                @else
                                                                    <a href="{{ asset($archivo) }}" target="_blank"
                                                                        class="text-blue-600 underline block">
                                                                        📄 {{ basename($archivo) }}
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Seguimientos -->
                                                @if (!empty($seguimientos))
                                                    <div class="ml-4 mt-2 space-y-3">
                                                        @foreach ($seguimientos as $segIndex => $seg)
                                                            @php
                                                                $evidenciasSeg =
                                                                    $seg['evidencias'] ?? ($seg['evidencia'] ?? []);
                                                                if (is_string($evidenciasSeg)) {
                                                                    $evidenciasSeg =
                                                                        json_decode($evidenciasSeg, true) ?? [];
                                                                }
                                                            @endphp

                                                            <div
                                                                class="border-l-2 border-yellow-300 pl-3 py-2 bg-yellow-50 rounded-sm">
                                                                <p><strong>Seguimiento {{ $segIndex + 1 }}:</strong>
                                                                </p>
                                                                <p><strong>Periodo:</strong>
                                                                    {{ decodeSafe($seg['periodo_consultar'] ?? '') }}
                                                                </p>
                                                                <p><strong>Observaciones:</strong>
                                                                    {{ decodeSafe($seg['observaciones'] ?? '') }}</p>
                                                                <p><strong>Estado:</strong>
                                                                    {{ decodeSafe($seg['estado'] ?? '') }}</p>
                                                                <p><strong>Documento:</strong>
                                                                    {{ decodeSafe($seg['documento'] ?? 'N/A') }}</p>

                                                                <!-- Evidencias del seguimiento -->
                                                                @if (!empty($evidenciasSeg))
                                                                    <div class="mt-2">
                                                                        <strong>📎 Evidencias del seguimiento:</strong>
                                                                        <div class="flex flex-wrap gap-2 mt-1">
                                                                            @foreach ($evidenciasSeg as $archivoSeg)
                                                                                @php $extensionSeg = pathinfo($archivoSeg, PATHINFO_EXTENSION); @endphp
                                                                                @if (in_array(strtolower($extensionSeg), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                                    <img src="{{ asset($archivoSeg) }}"
                                                                                        alt="Evidencia"
                                                                                        class="w-32 h-32 object-cover rounded shadow">
                                                                                @else
                                                                                    <a href="{{ asset($archivoSeg) }}"
                                                                                        target="_blank"
                                                                                        class="text-blue-600 underline block">
                                                                                        📄 {{ basename($archivoSeg) }}
                                                                                    </a>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('planes.descargarBackup', $backup->id) }}"
            class="bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-red-200 transition shadow">
            📄 Descargar PDF
        </a>
    </div>
</x-app-layout>
