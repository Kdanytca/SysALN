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
        <label class="block font-medium">Nombre del Usuario <i class="text-red-500">*</i></label>
        <input type="text" name="nombre_usuario" value="{{ $usuario->nombre_usuario}}"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Correo Electronico <i class="text-red-500">*</i></label>
        <input type="email" name="email" value="{{ $usuario->email}}"
            class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Contraseña</label>
        <input type="password" name="password" value=""
            class="w-full border rounded px-3 py-2">
        <p>Dejar el campo en blanco si no desea cambiar la contraseña</p>
    </div>
    
    <div class="mb-4">
        <label class="block font-medium">Institución</label>
        <select name="idInstitucion" class="w-full border rounded px-3 py-2">
            <option value="" {{ is_null($usuario->idInstitucion) ? 'selected' : '' }}>
                Sin institución
            </option>
            @foreach ($instituciones as $institucion)
                <option value="{{ $institucion->id }}"
                    {{ $usuario->idInstitucion == $institucion->id ? 'selected' : '' }}>
                    {{ $institucion->nombre_institucion }}
                </option>
            @endforeach
        </select>
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
        <label class="block font-medium">Tipo de Usuario <i class="text-red-500">*</i></label>
        <select name="tipo_usuario" required class="w-full border rounded px-3 py-2">
            <option value="administrador" {{ $usuario->tipo_usuario == 'administrador' ? 'selected' : '' }}>Administrador</option>
            <option value="encargado_institucion" {{ $usuario->tipo_usuario == 'encargado_institucion' ? 'selected' : '' }}>Encargado de Institución</option>
            <option value="encargado_departamento" {{ $usuario->tipo_usuario == 'encargado_departamento' ? 'selected' : '' }}>Encargado de Departamento</option>
            <option value="responsable_plan" {{ $usuario->tipo_usuario == 'responsable_plan' ? 'selected' : '' }}>Responsable de Plan Estratégico</option>
            <option value="responsable_meta" {{ $usuario->tipo_usuario == 'responsable_meta' ? 'selected' : '' }}>Responsable de Meta</option>
            <option value="responsable_actividad" {{ $usuario->tipo_usuario == 'responsable_actividad' ? 'selected' : '' }}>Responsable de Actividad</option>
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