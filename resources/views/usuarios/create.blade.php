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
        <input type="text" name="nombre_usuario"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Correo Electronico</label>
        <input type="email" name="email"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Contraseña</label>
        <input type="password" name="password"
            class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Institución</label>
        <select name="idInstitucion"
            class="w-full border rounded px-3 py-2">
            <option value="">Seleccione una institución</option>
            @foreach ($instituciones as $institucion)
            <option value="{{ $institucion->id }}">{{ $institucion->nombre_institucion }}</option>
            @endforeach
        </select>
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
        <select name="tipo_usuario" required class="w-full border rounded px-3 py-2">
            <option value="administrador">Administrador</option>
            <option value="encargado_institucion">Encargado de Institución</option>
            <option value="encargado_departamento">Encargado de Departamento</option>
            <option value="responsable_plan">Responsable de Plan Estratégico</option>
            <option value="responsable_meta">Responsable de Meta</option>
            <option value="responsable_actividad">Responsable de Actividad</option>
        </select>
    </div>

    <div class="flex justify-end">
        <button type="button" @click="{{ $closeModal }}"
            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Cancelar
        </button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Guardar
        </button>
    </div>
</form>