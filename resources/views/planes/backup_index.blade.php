<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800">
            🗄️ Respaldo de Planes Estratégicos
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <table
                class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg overflow-hidden shadow text-base text-gray-800">
                <thead class="bg-indigo-50 text-indigo-700 uppercase text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre Plan</th>
                        <th class="px-4 py-3 text-left">Departamento</th>
                        <th class="px-4 py-3 text-left">Responsable</th>
                        <th class="px-4 py-3 text-left">Fecha Inicio</th>
                        <th class="px-4 py-3 text-left">Fecha Fin</th>
                        <th class="px-4 py-3 text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($backups as $backup)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 font-medium">{{ $backup->nombre_plan_estrategico }}</td>
                            <td>{{ $backup->nombre_departamento ?? 'N/A' }}</td>
                            <td>{{ $backup->nombre_responsable ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($backup->fecha_inicio)->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($backup->fecha_fin)->format('d-m-Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('planes.verBackup', $backup->id) }}"
                                    class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-md text-sm hover:bg-blue-200 transition shadow-sm">
                                    Ver Respaldo
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay respaldos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
