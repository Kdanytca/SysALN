@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.tailwindcss.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaPlanes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Todos los Planes Estratégicos
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow border border-gray-200 sm:rounded-lg p-6 overflow-x-auto">
                <table id="tablaPlanes" class="min-w-full text-sm text-gray-800">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Institución</th>
                            <th class="px-4 py-3 text-left">Departamento</th>
                            <th class="px-4 py-3 text-left">Plan</th>
                            <th class="px-4 py-3 text-left">Ejes Estratégicos</th>
                            <th class="px-4 py-3 text-left">Objetivos</th>
                            <th class="px-4 py-3 text-left">Inicio</th>
                            <th class="px-4 py-3 text-left">Fin</th>
                            <th class="px-4 py-3 text-left">Responsable</th>
                            <th class="px-4 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($planes as $plan)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-4 py-2">{{ $plan->departamento->institucion->nombre_institucion }}</td>
                                <td class="px-4 py-2">{{ $plan->departamento->departamento }}</td>
                                <td class="px-4 py-2 font-semibold text-gray-900">
                                    {{ $plan->nombre_plan_estrategico }}
                                </td>

                                <!-- Ejes Estratégicos -->
                                <td class="px-4 py-2 max-w-xs">
                                    <div class="relative">
                                        @php
                                            $ejes = [];
                                            if ($plan->ejes_estrategicos) {
                                                $decodificado = json_decode($plan->ejes_estrategicos, true);
                                                if (is_array($decodificado)) {
                                                    $ejes = array_filter($decodificado);
                                                } else {
                                                    $ejes = array_map('trim', explode(',', $plan->ejes_estrategicos));
                                                }
                                            }
                                        @endphp

                                        <div class="overflow-hidden text-ellipsis max-h-20 whitespace-pre-line text-xs">
                                            @if (!empty($ejes))
                                                {{ implode("\n", array_map(fn($eje, $i) => $i + 1 . '. ' . str_replace("\n", ' ', $eje), $ejes, array_keys($ejes))) }}
                                            @else
                                                <span class="italic text-gray-400">Sin ejes estratégicos</span>
                                            @endif
                                        </div>

                                        @if (!empty($ejes))
                                            <button
                                                onclick="abrirModalTexto('Ejes - {{ $plan->nombre_plan_estrategico }}', {{ json_encode($ejes) }})"
                                                class="text-blue-600 text-xs mt-1 hover:underline">
                                                📋 Ver completo ({{ count($ejes) }})
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- Objetivos -->
                                <td class="px-4 py-2 max-w-xs">
                                    <div class="relative">
                                        @php
                                            $objetivos = [];
                                            if ($plan->objetivos) {
                                                $objetivos = json_decode($plan->objetivos, true);
                                                $objetivos = array_filter($objetivos);
                                            }
                                        @endphp

                                        <div class="overflow-hidden text-ellipsis max-h-20 whitespace-pre-line text-xs">
                                            @if (!empty($objetivos))
                                                {{ '• ' . implode("\n• ", $objetivos) }}
                                            @else
                                                <span class="italic text-gray-400">Sin objetivos</span>
                                            @endif
                                        </div>

                                        @if (!empty($objetivos))
                                            <button
                                                onclick="abrirModalTexto('Objetivos - {{ $plan->nombre_plan_estrategico }}', {{ json_encode($objetivos) }})"
                                                class="text-blue-600 text-xs mt-1 hover:underline">
                                                📋 Ver completo ({{ count($objetivos) }})
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-2 text-gray-600">
                                    {{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-2 text-gray-600">
                                    {{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ $plan->responsable->nombre_usuario ?? '—' }}
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('plan.metas', $plan->id) }}"
                                            class="bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-md text-xs hover:bg-indigo-200 transition shadow-sm">
                                            Metas
                                        </a>

                                        <a href="{{ route('planes.reporte', $plan->id) }}"
                                            class="bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-md text-xs hover:bg-indigo-200 transition shadow-sm">
                                            Ver Reporte
                                        </a>

                                        <form method="POST" action="{{ route('planes.finalizar', $plan->id) }}">
                                            @csrf
                                            @php
                                                $esFinalizado = $plan->indicador === 'finalizado';
                                                $clasesBoton = $esFinalizado
                                                    ? 'bg-orange-100 text-orange-800 hover:bg-orange-200'
                                                    : 'bg-teal-100 text-teal-800 hover:bg-teal-200';
                                            @endphp
                                            <button type="submit"
                                                class="{{ $clasesBoton }} px-3 py-1.5 rounded-md text-xs transition shadow-sm">
                                                {{ $esFinalizado ? 'Reanudar Plan' : 'Finalizar Plan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Botón Volver -->
                @auth
                    @php
                        switch (auth()->user()->tipo_usuario) {
                            case 'encargado_institucion':
                                $rutaInicio = route('institucion.ver', auth()->user()->idInstitucion);
                                break;
                            case 'administrador':
                                $rutaInicio = route('instituciones.index');
                                break;
                            default:
                                $rutaInicio = '#';
                        }
                    @endphp

                    @if ($rutaInicio !== '#')
                        <div class="mt-8">
                            <a href="{{ $rutaInicio }}"
                                class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Volver a instituciones
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Modal para ver texto completo -->
    <div id="modalTextoLargo" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="cerrarModalTexto()">✕</button>
            <h3 id="modalTextoTitulo" class="text-lg font-bold mb-4 text-indigo-700"></h3>
            <div id="modalTextoContenido" class="text-gray-800 whitespace-pre-wrap"></div>
        </div>
    </div>

    <script>
        function abrirModalTexto(titulo, contenido) {
            const modal = document.getElementById('modalTextoLargo');
            const tituloEl = document.getElementById('modalTextoTitulo');
            const contenidoEl = document.getElementById('modalTextoContenido');

            tituloEl.textContent = titulo;
            if (Array.isArray(contenido)) {
                contenidoEl.textContent = contenido.map((txt, i) => `${i + 1}. ${txt}`).join('\n');
            } else {
                contenidoEl.textContent = contenido;
            }

            modal.classList.remove('hidden');
        }

        function cerrarModalTexto() {
            document.getElementById('modalTextoLargo').classList.add('hidden');
        }
    </script>
</x-app-layout>
