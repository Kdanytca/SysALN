<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Models\Institucion;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        $usuarios = Usuario::with(['departamento'])->get();
        $departamentos = Departamento::all();
        $instituciones = Institucion::all();
        return view('usuarios.index', compact('usuarios', 'departamentos', 'instituciones'));
    }

    // Se encarga de crear un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|unique:usuarios,nombre_usuario',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'idDepartamento' => 'nullable|exists:departamentos,id',
            'idInstitucion' => 'nullable|exists:instituciones,id',
            'tipo_usuario' => 'required',
        ]);

        $usuario = Usuario::create([
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idDepartamento' => $request->idDepartamento,
            'idInstitucion' => $request->idInstitucion,
            'tipo_usuario' => $request->tipo_usuario,
            'remember_token' => $request->has('remember') ? $request->remember : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre_usuario' => $usuario->nombre_usuario,
                    'email' => $usuario->email,
                    'departamento' => $usuario->departamento->departamento ?? 'Sin Departamento',
                ]
            ], 200);
        }

        return redirect()->back()->with('success', 'Usuario registrado correctamente.');
    }

    //obtener usuarios por departamento
    public function usuariosPorDepartamento($id)
    {
        $usuarios = Usuario::where('idDepartamento', $id)
            ->whereDoesntHave('planEstrategico')
            ->get(['id', 'nombre_usuario']);

        return response()->json($usuarios);
    }



    // Se encarga de editar un usuario
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|unique:usuarios,nombre_usuario,' . $id,
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:8',
            'idDepartamento' => 'nullable|exists:departamentos,id',
            'idInstitucion' => 'nullable|exists:instituciones,id',
            'tipo_usuario' => 'required',
        ]);

        $usuario = Usuario::find($id);
        $usuario->update([
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'idDepartamento' => $request->idDepartamento,
            'idInstitucion' => $request->idInstitucion,
            'tipo_usuario' => $request->tipo_usuario,
        ]);

        // Solo actualiza el password si se proporcionó uno nuevo
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        // Obtener el nombre del nuevo departamento
        $nuevoDepartamento = $usuario->departamento->departamento ?? 'Sin departamento';

        // Actualizar todas las actividades del usuario con el nuevo nombre del departamento
        \App\Models\Actividad::where('idEncargadoActividad', $usuario->id)
            ->update(['unidad_encargada' => $nuevoDepartamento]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Elimina un usuario
    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
    // Muestra un usuario en formato JSON
    public function showJson($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario);
    }
}
