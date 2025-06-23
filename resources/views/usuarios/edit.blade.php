<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
    @method('PUT')
    @endif

    @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
            <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Nombre del Usuario</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario" value="{{ $usuario->nombre_usuario}}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Correo Electronico</label>
        <input type="email" name="correo" id="correo" value="{{ $usuario->correo}}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input type="password" name="contraseña" id="contraseña" value=""
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Departamento</label>
        <select name="idDepartamento" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="{{ $usuario->idDepartamento}}">{{ $usuario->departamentos->departamento}}</option>
            @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
        <select name="tipo_usuario" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            <option value="Administrador" {{ $usuario->tipo_usuario == 'Administrador' ? 'selected' : '' }}>Administrador
            </option>
            <option value="Colaborador" {{ $usuario->tipo_usuario == 'Colaborador' ? 'selected' : '' }}>Colaborador</option>
        </select>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="editModalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Actualizar
        </button>
    </div>
</form>