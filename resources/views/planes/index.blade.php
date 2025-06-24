<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Planes Estratégicos</h1>

            <button onclick="openCreateModal()"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                + Nuevo Plan
            </button>
        </div>

        <!-- Tabla -->
        <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Indicador</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($planes as $plan)
                    <tr>
                        <td class="px-6 py-4">{{ $plan->nombre_plan_estrategico }}</td>
                        <td class="px-6 py-4">{{ $plan->departamento->departamento ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $plan->responsable->nombre_usuario ?? 'Sin responsable' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $color = match ($plan->indicador) {
                                    'rojo' => 'bg-red-500',
                                    'amarillo' => 'bg-yellow-400',
                                    'verde' => 'bg-green-500',
                                    default => 'bg-gray-300',
                                };
                            @endphp
                            <span class="inline-block w-5 h-5 rounded-full {{ $color }}"
                                title="{{ ucfirst($plan->indicador) }}"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                            <a href="{{ route('metas.create', $plan->id) }}"
                                class="text-blue-600 hover:text-blue-900 font-semibold">
                                Metas
                            </a>
                            <button class="text-yellow-600 hover:text-yellow-800 font-semibold"
                                onclick='openEditModal({
        id: {{ $plan->id }},
        nombre_plan_estrategico: @json($plan->nombre_plan_estrategico),
        ejes_estrategicos: @json($plan->ejes_estrategicos),
        fecha_inicio: @json($plan->fecha_inicio),
        fecha_fin: @json($plan->fecha_fin),
        idDepartamento: {{ $plan->idDepartamento }},
        idUsuario: {{ $plan->idUsuario }}
    })'>
                                Editar
                            </button>


                            <form action="{{ route('planes.destroy', $plan->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('¿Estás seguro de eliminar este plan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay planes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative">
            <button class="absolute top-2 right-2 text-gray-600 hover:text-red-600"
                onclick="document.getElementById('modal').classList.add('hidden')">
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Nuevo Plan Estratégico</h2>

            <form method="POST" action="{{ route('planes.store') }}">
                @csrf
                <input type="hidden" name="plan_id" id="plan_id">

                <!-- Institución -->
                <div class="mb-4">
                    <label for="institucion" class="block font-medium">Institución</label>
                    <select id="institucion" class="w-full border rounded px-3 py-2" onchange="filtrarDepartamentos()"
                        required>
                        <option value="">Seleccione una institución</option>
                        @foreach ($instituciones as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->nombre_institucion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Departamento -->
                <div class="mb-4">
                    <label for="idDepartamento" class="block font-medium">Departamento</label>
                    <select name="idDepartamento" id="idDepartamento" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccione un departamento</option>
                        <!-- Se llenará dinámicamente -->
                    </select>
                </div>


                <!-- Nombre -->
                <div class="mb-4">
                    <label for="nombre_plan_estrategico" class="block font-medium">Nombre del Plan</label>
                    <input type="text" name="nombre_plan_estrategico" class="w-full border rounded px-3 py-2"
                        required>
                </div>

                <!-- Ejes -->
                <div class="mb-4">
                    <label for="ejes_estrategicos" class="block font-medium">Ejes Estratégicos</label>
                    <input type="text" name="ejes_estrategicos" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <label for="fecha_inicio" class="block font-medium">Inicio</label>
                        <input type="date" name="fecha_inicio" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="w-1/2">
                        <label for="fecha_fin" class="block font-medium">Fin</label>
                        <input type="date" name="fecha_fin" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="responsable" class="block font-medium">Responsable</label>
                    <select name="responsable" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccione un responsable</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre_usuario }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Guardar Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(plan) {
            const modal = document.getElementById('modal');
            const form = modal.querySelector('form');
            const methodInput = form.querySelector('input[name="_method"]');
            const action = `/planes/${plan.id}`;

            form.reset();
            form.action = action;

            // Eliminar _method si ya existe
            if (methodInput) methodInput.remove();

            // Agregar campo _method para PUT
            const hiddenMethod = document.createElement('input');
            hiddenMethod.type = 'hidden';
            hiddenMethod.name = '_method';
            hiddenMethod.value = 'PUT';
            form.appendChild(hiddenMethod);

            // Rellenar campos
            document.getElementById('plan_id').value = plan.id;
            form.nombre_plan_estrategico.value = plan.nombre_plan_estrategico;
            form.ejes_estrategicos.value = plan.ejes_estrategicos;
            form.fecha_inicio.value = plan.fecha_inicio;
            form.fecha_fin.value = plan.fecha_fin;
            form.idDepartamento.value = plan.idDepartamento;
            form.responsable.value = plan.idUsuario;

            modal.classList.remove('hidden');
        }

        function openCreateModal() {
            const modal = document.getElementById('modal');
            const form = modal.querySelector('form');
            form.reset();
            form.action = "{{ route('planes.store') }}";

            // Eliminar _method si existe
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            modal.classList.remove('hidden');
        }
    </script>
    <script>
        const instituciones = @json($instituciones);

        function filtrarDepartamentos() {
            const institucionId = document.getElementById('institucion').value;
            const departamentoSelect = document.getElementById('idDepartamento');

            // Limpia los options actuales
            departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';

            if (institucionId === '') return;

            const institucion = instituciones.find(i => i.id == institucionId);
            if (institucion && institucion.departamentos.length > 0) {
                institucion.departamentos.forEach(dep => {
                    const option = document.createElement('option');
                    option.value = dep.id;
                    option.textContent = dep.departamento;
                    departamentoSelect.appendChild(option);
                });
            }
        }
    </script>

</x-app-layout>
