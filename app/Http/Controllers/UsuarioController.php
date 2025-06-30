<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Models\Institucion;
use App\Models\Departamento;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        $usuarios = Usuario::with(['departamento'])->get();
        $departamentos = Departamento::all();
        return view('usuarios.index', compact('usuarios', 'departamentos'));
    }

    // Se encarga de crear un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:255|unique:usuarios,nombre_usuario',
            'correo' => 'required|email|max:255|unique:usuarios,correo',
            'contraseña' => 'required|string|min:8',
            'idDepartamento' => 'required|exists:departamentos,id',
            'tipo_usuario' => 'required',
        ]);

        $usuario = new Usuario();
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->correo = $request->correo;
        $usuario->contraseña = bcrypt($request->contraseña);
        $usuario->idDepartamento = $request->idDepartamento;
        $usuario->tipo_usuario = $request->tipo_usuario;
        $usuario->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'usuario' => $usuario,
            ], 200);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    //optener usuarios por departamento
    public function usuariosPorDepartamento($id)
    {
        $usuarios = Usuario::where('idDepartamento', $id)
            ->whereDoesntHave('planesEstrategicos') // opcional por si no se quiere repetir responsables
            ->get();

        return response()->json($usuarios);
    }

    // Se encarga de editar un usuario
    public function update(Request $request, string $id)
    {
        request()->validate([
            'nombre_usuario' => 'required|string|max:255',
            'correo' => 'required|email|max:255|unique:usuarios,correo,' . $id,
            'contraseña' => 'nullable|string|min:8',
            'idDepartamento' => 'required|exists:departamentos,id',
            'tipo_usuario' => 'required',
        ]);

        $usuario = Usuario::find($id);
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->correo = $request->correo;
        if ($request->filled('contraseña')) {
            $usuario->contraseña = bcrypt($request->contraseña);
        }
        $usuario->idDepartamento = $request->idDepartamento;
        $usuario->tipo_usuario = $request->tipo_usuario;
        $usuario->remember_token = $request->has('remember') ? $request->remember : null;
        $usuario->save();

        // Obtener el nombre del nuevo departamento
        $nuevoDepartamento = $usuario->departamento->departamento;

        // Actualizar todas las actividades del usuario con el nuevo nombre del departamento
        \App\Models\Actividad::where('idUsuario', $usuario->id)
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
