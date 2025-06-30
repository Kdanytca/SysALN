@if ($seguimientos->isEmpty())
    <div class="bg-yellow-50 text-yellow-800 px-4 py-3 rounded shadow-sm text-sm">
        No hay seguimientos registrados.
    </div>
@else
    <div id="contenidoSeguimientos" class="mt-6">
        <div class="overflow-hidden border border-gray-200 rounded-lg shadow bg-white">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left border-b">Periodo</th>
                        <th class="px-4 py-3 text-left border-b">Estado</th>
                        <th class="px-4 py-3 text-left border-b">Observaciones</th>
                        <th class="px-4 py-3 text-left border-b">Fecha</th>
                        <th class="px-4 py-3 text-left border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($seguimientos as $seguimiento)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($seguimiento->periodo_consultar)->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-2 capitalize">
                                @php
                                    $colorClass = match ($seguimiento->estado) {
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en progreso' => 'bg-blue-100 text-blue-800',
                                        'finalizado' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $colorClass }}">
                                    {{ $seguimiento->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ $seguimiento->observaciones ?? '—' }}
                            </td>
                            <td class="px-4 py-2 text-gray-500">
                                {{ $seguimiento->created_at->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" onclick="abrirModalEditarSeguimiento({{ $seguimiento->toJson() }})"
                                    class="text-indigo-600 hover:underline text-sm font-medium">
                                    Editar
                                </a>

                                <button type="button" onclick="eliminarSeguimiento({{ $seguimiento->id }})"
                                    class="text-red-600 hover:underline text-sm font-medium">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
