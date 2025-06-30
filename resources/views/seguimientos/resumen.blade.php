<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <div
                class="inline-flex items-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-md shadow-sm hover:bg-indigo-100 transition duration-200">
                <a href="{{ route('meta.actividades', $meta->id) }}"
                    class="flex items-center space-x-1 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Volver a actividades</span>
                </a>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-gray-700 mb-6">
            Resumen de Seguimientos de la Meta:
            <span class="text-indigo-500">"{{ $meta->nombre_meta }}"</span>
        </h1>

        @if ($actividades->isEmpty())
            <div class="bg-yellow-50 text-yellow-800 px-4 py-3 rounded shadow-sm text-sm">
                No hay actividades registradas.
            </div>
        @else
            <div class="bg-white shadow overflow-hidden border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Actividad</th>
                            <th class="px-6 py-3 text-left">Último Estado</th>
                        </tr>
                    </thead>

                    @foreach ($actividades as $actividad)
                        <tbody x-data="{ abierto: false }" class="bg-white divide-y divide-gray-100">
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-900">
                                    {{ $actividad->nombre_actividad }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $ultimo = $actividad->seguimientos->last();
                                    @endphp

                                    @if ($ultimo)
                                        @php
                                            $colorClass = match ($ultimo->estado) {
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'en progreso' => 'bg-blue-100 text-blue-800',
                                                'finalizado' => 'bg-green-100 text-green-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp

                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $colorClass }}">
                                            {{ ucfirst($ultimo->estado) }}
                                        </span>
                                        <span class="text-gray-500 text-xs ml-2">
                                            ({{ \Carbon\Carbon::parse($ultimo->periodo_consultar)->format('d-m-Y') }})
                                        </span>

                                        @if ($actividad->seguimientos->count() > 1)
                                            <button @click="abierto = !abierto"
                                                class="ml-4 inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                                                Ver anteriores
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Sin seguimiento</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($actividad->seguimientos->count() > 1)
                                <tr x-show="abierto" x-cloak>
                                    <td colspan="2" class="px-6 py-4 bg-gray-50 rounded-b">
                                        <ul class="space-y-2 text-sm text-gray-700 list-disc list-inside">
                                            @foreach ($actividad->seguimientos->slice(0, -1)->reverse() as $seguimiento)
                                                @php
                                                    $colorClass = match ($seguimiento->estado) {
                                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                        'en progreso' => 'bg-blue-100 text-blue-800',
                                                        'finalizado' => 'bg-green-100 text-green-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <li>
                                                    <span class="text-gray-600">
                                                        {{ \Carbon\Carbon::parse($seguimiento->periodo_consultar)->format('d-m-Y') }}
                                                    </span>
                                                    |
                                                    <span
                                                        class="px-2 py-0.5 rounded text-xs font-semibold {{ $colorClass }}">
                                                        {{ ucfirst($seguimiento->estado) }}
                                                    </span>
                                                    @if ($seguimiento->observaciones)
                                                        | <span
                                                            class="text-gray-500">{{ $seguimiento->observaciones }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    @endforeach
                </table>
            </div>
        @endif

    </div>
</x-app-layout>
