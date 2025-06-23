<form method="POST" action="{{ route('usuarios.store') }}">
    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre del Usuario</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Correo Electronico</label>
        <input type="email" name="correo" id="correo"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input type="password" name="contraseña" id="contraseña"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Departamento</label>
        <select name="idDepartamento" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="">Seleccione un departamento</option>
            @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
        <select name="tipo_usuario" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="Administrador">Administrador</option>
            <option value="Colaborador">Colaborador</option>
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