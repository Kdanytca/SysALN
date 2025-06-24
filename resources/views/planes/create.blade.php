<x-app-layout>
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold mb-6">Crear Plan Estratégico para {{ $institucion->nombre_institucion }}</h2>

        <form method="POST" action="{{ route('planes.store') }}">
            @csrf

            <!-- Departamento -->
            <div class="mb-4">
                <label for="idDepartamento" class="block font-medium">Departamento</label>
                <select name="idDepartamento" id="idDepartamento" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccione un departamento</option>
                    @foreach ($institucion->departamentos as $departamento)
                        <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nombre del Plan -->
            <div class="mb-4">
                <label for="nombre_plan_estrategico" class="block font-medium">Nombre del Plan</label>
                <input type="text" name="nombre_plan_estrategico" id="nombre_plan_estrategico"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Ejes Estratégicos -->
            <div class="mb-4">
                <label for="ejes_estrategicos" class="block font-medium">Ejes Estratégicos</label>
                <input type="text" name="ejes_estrategicos" id="ejes_estrategicos"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Fechas -->
            <div class="mb-4">
                <label for="fecha_inicio" class="block font-medium">Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full border rounded px-3 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label for="fecha_fin" class="block font-medium">Fecha de Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="w-full border rounded px-3 py-2" required>
            </div>


            <!-- Responsable -->
            <div class="mb-4">
                <label for="responsable" class="block font-medium">Responsable</label>

                @if ($usuariosDisponibles->isEmpty())
                    <div class="text-red-600 font-semibold">
                        No hay usuarios disponibles. <a href="{{ route('usuarios.create') }}"
                            class="underline text-blue-600">Registra uno aquí</a>.
                    </div>
                @else
                    <select name="responsable" id="responsable" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach ($usuariosDisponibles as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->nombre_usuario }}</option>
                        @endforeach
                    </select>

                @endif
            </div>


            <!-- Botón -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Guardar Plan Estratégico
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
