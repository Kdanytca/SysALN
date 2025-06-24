<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Models\Institucion;
use App\Models\Departamento;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::with(['departamento'])->get();
        $departamentos = Departamento::all();
        return view('usuarios.index', compact('usuarios', 'departamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departamentos = Departamento::all();
        return view('usuarios.create', compact('departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'nombre_usuario' => 'required|string|max:255',
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

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $usuario = Usuario::find($id);
        $departamentos = Departamento::all();
        return view('usuarios.edit', ['usuario' => $usuario], compact('departamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
