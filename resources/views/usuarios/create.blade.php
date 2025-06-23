<form method="POST" action="{{ route('usuarios.store') }}">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre del Usuario</label>
        <input type="text" name="nombre" id="nombre"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Correo Electronico</label>
        <input type="email" name="email" id="email"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input type="password" name="password" id="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Institución</label>
        <select name="institucion_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $institucion)
            <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Departamento</label>
        <select name="departamento_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="">Seleccione un departamento</option>
            @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Departamento</label>
        <select name="tipo" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="administrador">Administrador</option>
            <option value="colaborador">Colaborador</option>
        </select>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="modalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Guardar
        </button>
    </div>
</form>