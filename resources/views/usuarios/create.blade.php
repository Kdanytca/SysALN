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
        <label class="block font-medium">Nombre del Usuario</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Correo Electronico</label>
        <input type="email" name="email" id="email"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Contraseña</label>
        <input type="password" name="password" id="password"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Departamento</label>
        <select name="idDepartamento"
            class="w-full border rounded px-3 py-2">
            <option value="">Seleccione un departamento</option>
            @foreach ($departamentos as $departamento)
            <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Tipo de Usuario</label>
        <select name="tipo_usuario" required
            class="w-full border rounded px-3 py-2">
            <option value="Administrador">Administrador</option>
            <option value="Colaborador">Colaborador</option>
        </select>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="modalOpen = false"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Guardar
        </button>
    </div>
</form>