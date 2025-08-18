<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4">Resultados del Plan: {{ $plan->nombre_plan_estrategico }}</h1>

        @if ($plan->indicador === 'finalizado')
            <div class="mb-6 p-6 bg-purple-100 text-purple-800 rounded shadow-sm text-base">
                <strong class="block text-lg mb-2">⛔ Este plan está finalizado.</strong>
                <p class="mb-2">El progreso y el estado de tiempo no se actualizarán hasta que se reactive.</p>

                <div class="grid grid-cols-3 gap-6 text-purple-700 mt-4">
                    <div>
                        <p class="font-semibold">📅 Fecha de inicio</p>
                        <p>{{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">✅ Fecha de finalización</p>
                        <p>{{ \Carbon\Carbon::parse($plan->updated_at)->format('d/m/Y') }}</p>
                    </div>
                    @php
                        $inicio = \Carbon\Carbon::parse($plan->fecha_inicio);
                        $finReal = \Carbon\Carbon::parse($plan->updated_at);
                        $finEstimada = \Carbon\Carbon::parse($plan->fecha_fin); // Fecha esperada
                        $diferenciaHoras = $inicio->diffInHours($finReal);
                        $duracionDiasAprox = round($diferenciaHoras / 24);

                        $mensajeTiempo = $finReal->lte($finEstimada)
                            ? '✅ El plan se completó en el tiempo estipulado.'
                            : '⚠️ El plan se completó después del tiempo previsto.';
                    @endphp

                    <div>
                        <p class="font-semibold">🕓 Duración total</p>
                        <p>{{ $duracionDiasAprox }} días</p>
                    </div>
                </div>

                <div
                    class="mt-4 p-4 rounded text-white {{ $finReal->lte($finEstimada) ? 'bg-green-400' : 'bg-yellow-400' }}">
                    {{ $mensajeTiempo }}
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 mb-4">
                <div class="sm:w-1/2 p-4 bg-blue-100 text-blue-800 rounded">
                    Avance global al finalizar el plan: <strong>{{ $porcentajeTotal }}%</strong>
                </div>
                <div class="sm:w-1/2 p-4 bg-blue-50 text-blue-900 rounded text-sm">
                    <div class="font-semibold mb-1">📌 Estado final</div>
                    <div>Este fue el porcentaje alcanzado al momento de finalizar el plan.</div>
                </div>
            </div>
        @endif

        @if (isset($porcentajeTotal))
            @if ($plan->indicador !== 'finalizado')
                @php
                    $hoy = \Carbon\Carbon::today();
                    $inicio = \Carbon\Carbon::parse($plan->fecha_inicio);
                    $fin = \Carbon\Carbon::parse($plan->fecha_fin);

                    $totalDias = $inicio->diffInDays($fin);
                    $diasTranscurridos = $inicio->diffInDays($hoy);
                    $diasRestantes = $hoy->lessThanOrEqualTo($fin) ? $hoy->diffInDays($fin) : 0;
                    $diasRetrasados = $hoy->greaterThan($fin) ? $fin->diffInDays($hoy) : 0;

                    $estadoTiempo = match ($plan->indicador) {
                        'verde' => '✅ El plan va bien según el tiempo estimado.',
                        'amarillo' => '🟡 Atención: el plan ha pasado la mitad del tiempo.',
                        'rojo' => '🔴 El plan está atrasado según la fecha final.',
                        'blanco' => '🕒 El plan está en su etapa inicial.',
                        default => '⏳ Sin estado definido.',
                    };

                    $detalleTiempo = 'Inicio: ' . $inicio->format('d M Y') . ' | Fin: ' . $fin->format('d M Y') . ' | ';

                    if ($diasRetrasados > 0) {
                        $detalleTiempo .= "⏰ $diasRetrasados días de retraso.";
                    } else {
                        $detalleTiempo .= "$diasTranscurridos días transcurridos, $diasRestantes días restantes.";
                    }

                    $colorFondo = match ($plan->indicador) {
                        'verde' => 'green-100',
                        'amarillo' => 'yellow-100',
                        'rojo' => 'red-100',
                        default => 'gray-100',
                    };

                    $colorTexto = match ($plan->indicador) {
                        'verde' => 'green-800',
                        'amarillo' => 'yellow-800',
                        'rojo' => 'red-800',
                        default => 'gray-800',
                    };
                @endphp

                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <div class="sm:w-1/2 p-4 bg-blue-100 text-blue-800 rounded">
                        Avance global del plan estratégico: <strong>{{ $porcentajeTotal }}%</strong>
                    </div>
                    <div class="sm:w-1/2 p-4 bg-{{ $colorFondo }} text-{{ $colorTexto }} rounded text-sm">
                        <div class="font-semibold mb-1">{{ $estadoTiempo }}</div>
                        <div>{{ $detalleTiempo }}</div>
                    </div>
                </div>
            @endif
        @endif

        <div class="mt-10">
            <h2 class="text-xl font-semibold mb-4">Avance por Meta</h2>
            <canvas id="graficaAvancePorMeta" class="w-full max-h-48"></canvas>
        </div>

        @if (empty($data))
            <p class="text-gray-500">Este plan no tiene metas con actividades registradas aún.</p>
        @else
            <table class="min-w-full mt-6 border border-gray-300 rounded">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Meta</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Porcentaje</th>
                        <th class="px-4 py-2 text-left">Indicador</th>
                        <th class="px-4 py-2 text-left">Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $item['meta'] }}</td>
                            <td class="px-4 py-2">{{ $item['descripcion'] }}</td>
                            <td class="px-4 py-2">{{ $item['porcentaje'] }}%</td>
                            <td class="px-4 py-2">{{ ucfirst($item['indicador']) }}</td>
                            <td class="px-4 py-2">{{ $item['comentario'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @auth
            @if (in_array(auth()->user()->tipo_usuario, ['administrador', 'responsable_plan', 'encargado_institucion']))
                @php
                    switch (auth()->user()->tipo_usuario) {
                        case 'responsable_plan':
                            $rutaInicio = route('plan.responsable', $plan->id);
                            break;
                        case 'administrador':
                            $rutaInicio = route('institucion.planes', $plan->departamento->institucion->id);
                            break;
                        default:
                            $rutaInicio = '#';
                    }
                @endphp

                @if ($rutaInicio !== '#')
                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ $rutaInicio }}" class="text-blue-600 hover:underline">
                            ← Volver a planes
                        </a>

                        <a href="{{ route('plan.reporte.pdf', $plan->id) }}"
                            class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                            📄 Descargar Reporte PDF
                        </a>
                    </div>
                @endif
            @endif
        @endauth
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctxMeta = document.getElementById('graficaAvancePorMeta')?.getContext('2d');
        if (ctxMeta) {
            new Chart(ctxMeta, {
                type: 'bar',
                data: {
                    labels: @json(collect($data)->pluck('meta')),
                    datasets: [{
                        label: 'Avance %',
                        data: @json(collect($data)->pluck('porcentaje')),
                        backgroundColor: 'rgba(59,130,246,0.6)', // Azul suave
                        borderRadius: 8,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 12
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#e5e7eb'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>


    <script>
        const ctx = document.getElementById('graficaResultados')?.getContext('2d');
        if (ctx) {
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($fechas),
                    datasets: [{
                        label: 'Porcentaje de Seguimiento',
                        data: @json($porcentajes),
                        backgroundColor: 'rgba(147,197,253,0.5)',
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
