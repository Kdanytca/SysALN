<form method="GET" action="{{ route('historial_sesion.index') }}" class="mb-4 space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
    <div>
        <label class="block text-sm font-medium text-gray-700">Buscar usuario</label>
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Fecha de sesión desde</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Fecha de cierre hasta</label>
        <input type="date" name="fecha_cierre" id="fecha_cierre" value="{{ request('fecha_cierre') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Hora de sesión desde</label>
        <input type="time" name="hora_inicio" id="hora_inicio" value="{{ request('hora_inicio') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Hora de sesión hasta</label>
        <input type="time" name="hora_cierre" id="hora_cierre" value="{{ request('hora_cierre') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Estado</label>
        <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Todos</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="cerrado" {{ request('estado') === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
            <option value="expirado" {{ request('estado') === 'expirado' ? 'selected' : '' }}>Expirada</option>
        </select>
    </div>

    <div class="flex space-x-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">
            Filtrar
        </button>
        <a href="{{ route('historial_sesion.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md shadow hover:bg-gray-300">
            Limpiar
        </a>
    </div>
</form>
