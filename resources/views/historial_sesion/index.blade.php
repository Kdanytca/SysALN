<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Sesiones') }}
        </h2>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('historial_sesion._filtros')
        </div>
    </div>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="w-full table-fixed border border-gray-300 rounded-lg overflow-hidden shadow text-sm text-gray-800">
                        <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs font-semibold">
                            <tr>
                                <th class="w-1/5 px-4 py-3 text-left">Usuario</th>
                                <th class="w-1/5 px-4 py-3 text-left">Inicio de Sesión</th>
                                <th class="w-1/5 px-4 py-3 text-left">Cierre de Sesión</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($historial as $sesion)
                                <tr class="hover:bg-indigo-50 transition">
                                    <td class="px-4 py-3 font-medium truncate">{{ $sesion->nombre_usuario }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-indigo-600">
                                            {{ \Carbon\Carbon::parse($sesion->login_at)->format('d-m-Y') }}
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            {{ \Carbon\Carbon::parse($sesion->login_at)->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $status = null;
                                            $fecha = null;
                                            $hora = null;

                                            if ($sesion->logout_at) {
                                                // Caso normal: cerró sesión
                                                $status = 'Cerrada';
                                                $fecha = \Carbon\Carbon::parse($sesion->logout_at)->format('d-m-Y');
                                                $hora = \Carbon\Carbon::parse($sesion->logout_at)->format('H:i:s');
                                            } elseif ($sesion->last_activity && \Carbon\Carbon::parse($sesion->last_activity)->diffInMinutes(now()) > 15) {
                                                // Caso de inactividad mayor a 15 min → expirada
                                                $status = 'Expirada';
                                                $fecha = \Carbon\Carbon::parse($sesion->last_activity)->format('d-m-Y');
                                                $hora = \Carbon\Carbon::parse($sesion->last_activity)->format('H:i:s');
                                            } else {
                                                // Sesión sigue activa
                                                $status = 'Activo';
                                            }
                                        @endphp

                                        @if($status === 'Activo')
                                            <div class="font-semibold text-green-600">
                                                {{ $status }}
                                            </div>
                                        @else
                                            <div class="font-semibold text-indigo-600">
                                                {{ $fecha ? $fecha . ' (' . $status . ')' : $status }}
                                            </div>
                                            <div class="text-gray-600 text-sm">
                                                {{ $hora ?? '' }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $historial->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaCierre = document.getElementById('fecha_cierre');
            const horaInicio = document.getElementById('hora_inicio');
            const horaCierre = document.getElementById('hora_cierre');

            function toggleHoraInputs() {
                horaInicio.disabled = !fechaInicio.value;
                horaCierre.disabled = !fechaCierre.value;
            }

            // Ejecutar al cargar la página
            toggleHoraInputs();

            // Escuchar cambios
            fechaInicio.addEventListener('input', toggleHoraInputs);
            fechaCierre.addEventListener('input', toggleHoraInputs);
        });
    </script>

</x-app-layout>