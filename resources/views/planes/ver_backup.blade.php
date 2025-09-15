<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800">
            📑 Respaldo del Plan:
            <span class="text-indigo-600">{{ $backup->nombre_plan_estrategico }}</span>
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">

        <!-- Información General -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 mb-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-base text-gray-700">
                <p><span class="font-bold text-gray-900">📂 Departamento:</span>
                    {{ $backup->nombre_departamento }}</p>
                <p><span class="font-bold text-gray-900">👤 Responsable:</span>
                    {{ $backup->nombre_responsable }}</p>
                <p><span class="font-bold text-gray-900">📅 Fecha Inicio:</span> {{ $backup->fecha_inicio }}</p>
                <p><span class="font-bold text-gray-900">📅 Fecha Fin:</span> {{ $backup->fecha_fin }}</p>
            </div>
        </div>

        <!-- Ejes Estratégicos y Objetivos en la misma fila -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ejes Estratégicos -->
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-3">🎯 Ejes Estratégicos del Plan</h3>
                <p class="text-gray-700 leading-relaxed text-base break-words">
                    @foreach (explode(',', $backup->ejes_estrategicos) as $eje)
                        • {{ trim($eje) }} <br>
                    @endforeach
                </p>
            </div>

            <!-- Objetivos -->
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-3">📌 Objetivos</h3>
                @if ($backup->objetivos)
                    <ul class="list-disc list-inside space-y-2 text-gray-700 text-base break-words">
                        @foreach (json_decode($backup->objetivos) as $objetivo)
                            <li>{{ $objetivo }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="italic text-gray-500 text-base">Sin objetivos</p>
                @endif
            </div>
        </div>


        <!-- Metas, Actividades y Seguimientos (Acordeón Visual) -->
        @if ($backup->metas)
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">🎯 Metas</h3>

                @foreach (json_decode($backup->metas) as $index => $meta)
                    <div x-data="{ open: false }" class="border border-blue-300 rounded-lg bg-blue-50 shadow-sm">
                        <button @click="open = !open"
                            class="w-full px-4 py-3 text-left font-semibold text-blue-800 hover:bg-blue-100 rounded-t-lg">
                            Meta {{ $index + 1 }}: {{ $meta->nombre_meta }}
                            @if ($meta->responsable)
                                (Encargado: {{ $meta->responsable }})
                            @endif
                        </button>

                        <div x-show="open" class="px-4 py-3 space-y-2">
                            <p><strong>Ejes Estratégicos:</strong> {{ $meta->ejes_estrategicos }}</p>
                            <p><strong>Resultados Esperados:</strong> {{ $meta->resultados_esperados ?? 'N/A' }}</p>
                            <p><strong>Indicador:</strong> {{ $meta->indicador_resultados ?? 'N/A' }}</p>
                            <p><strong>Comentario:</strong> {{ $meta->comentario ?? 'N/A' }}</p>
                            <p><strong>Fecha Inicio:</strong> {{ $meta->fecha_inicio }} | <strong>Fecha Fin:</strong>
                                {{ $meta->fecha_fin }}</p>

                            <!-- Actividades -->
                            @if (!empty($meta->actividades))
                                <div class="ml-4 mt-2 space-y-2">
                                    @foreach ($meta->actividades as $actIndex => $actividad)
                                        <div x-data="{ open: false }"
                                            class="border border-green-300 rounded-lg bg-green-50 shadow-sm">
                                            <button @click="open = !open"
                                                class="w-full px-3 py-2 text-left font-medium text-green-800 hover:bg-green-100 rounded-t-lg">
                                                Actividad {{ $actIndex + 1 }}: {{ $actividad->nombre_actividad }}
                                                @if ($actividad->encargado)
                                                    (Encargado: {{ $actividad->encargado }})
                                                @endif
                                            </button>

                                            <div x-show="open" class="px-3 py-2 space-y-1">
                                                <p><strong>Objetivos:</strong> {{ $actividad->objetivos ?? 'N/A' }}</p>
                                                <p><strong>Comentario:</strong> {{ $actividad->comentario ?? 'N/A' }}
                                                </p>
                                                <p><strong>Unidad Encargada:</strong>
                                                    {{ $actividad->unidad_encargada ?? 'N/A' }}</p>
                                                <p><strong>Fecha Inicio:</strong> {{ $actividad->fecha_inicio }} |
                                                    <strong>Fecha Fin:</strong> {{ $actividad->fecha_fin }}
                                                </p>

                                                <!-- Seguimientos -->
                                                @if (!empty($actividad->seguimientos))
                                                    <div class="ml-4 mt-2 space-y-1">
                                                        @foreach ($actividad->seguimientos as $segIndex => $seg)
                                                            <div
                                                                class="border-l-2 border-yellow-300 pl-3 py-1 bg-yellow-50 rounded-sm">
                                                                <p><strong>Seguimiento {{ $segIndex + 1 }}:</strong>
                                                                </p>
                                                                <p>Periodo: {{ $seg->periodo_consultar }}</p>
                                                                <p>Observaciones: {{ $seg->observaciones }}</p>
                                                                <p>Estado: {{ $seg->estado }}</p>
                                                                <p>Documento: {{ $seg->documento ?? 'N/A' }}</p>
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
</x-app-layout>
