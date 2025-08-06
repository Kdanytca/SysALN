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
            <div class="bg-white shadow border border-gray-200 sm:rounded-lg p-6">
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
                                <td class="px-4 py-2">
                                    @foreach (explode(',', $plan->ejes_estrategicos) as $eje)
                                        <span
                                            class="inline-block bg-gray-100 text-gray-700 rounded-full px-3 py-1 text-xs font-medium mr-1 mb-1">
                                            {{ trim($eje) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @if ($plan->objetivos)
                                        @foreach (json_decode($plan->objetivos) as $objetivo)
                                            <div class="text-gray-800 text-xs mb-1">• {{ $objetivo }}</div>
                                        @endforeach
                                    @else
                                        <span class="italic text-gray-400">Sin objetivos</span>
                                    @endif
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
                                    <div class="flex space-x-2">
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
                                                    ? 'bg-orange-100 text-orange-800 hover:bg-orange-200' // Reanudar
                                                    : 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200'; // Finalizar (mismo azul)
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

                <!-- Botón Volver estilizado -->
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
                                $rutaInicio = '#'; // o nada
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
</x-app-layout>
