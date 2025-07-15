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
        <label class="block font-medium">Nombre del Usuario</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario" value="{{ $usuario->nombre_usuario}}"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Correo Electronico</label>
        <input type="email" name="email" id="email" value="{{ $usuario->email}}"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Contraseña</label>
        <input type="password" name="password" id="password" value=""
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Departamento</label>
        <select name="idDepartamento" class="w-full border rounded px-3 py-2">
            {{-- Opción para no tener ningún departamento --}}
            <option value="" {{ is_null($usuario->idDepartamento) ? 'selected' : '' }}>
                Sin departamento
            </option>

            {{-- Listado de todos los departamentos --}}
            @foreach ($departamentos as $departamento)
                <option value="{{ $departamento->id }}"
                    {{ $usuario->idDepartamento == $departamento->id ? 'selected' : '' }}>
                    {{ $departamento->departamento }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Tipo de Usuario</label>
        <select name="tipo_usuario" required
            class="w-full border rounded px-3 py-2">
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
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Actualizar
        </button>
    </div>
</form>