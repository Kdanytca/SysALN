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
        $usuarios = Usuario::with(['instituciones', 'departamentos'])->get();
        $instituciones = Institucion::all();
        $departamentos = Departamento::all();
        return view('usuarios.index', compact('usuarios', 'instituciones', 'departamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instituciones = Institucion::all();
        $departamentos = Departamento::all();
        return view('usuarios.create', compact('instituciones', 'departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'password' => 'required|string|min:8', // Asegúrate de que el campo 'password_confirmation' esté presente en el formulario
            'institucion_id' => 'required|exists:instituciones,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'tipo' => 'required',
        ]);

        $usuario = new Usuario();
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password); // Asegúrate de que el campo 'password' esté presente en el formulario
        $usuario->institucion_id = $request->institucion_id;
        $usuario->departamento_id = $request->departamento_id;
        $usuario->tipo = $request->tipo;
        
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
        $instituciones = Institucion::all();
        $departamentos = Departamento::all();
        return view('usuarios.edit', ['usuario' => $usuario], compact('instituciones', 'departamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:8', // Asegúrate de que el campo 'password_confirmation' esté presente en el formulario
            'institucion_id' => 'required|exists:instituciones,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'tipo' => 'required',
        ]);

        $usuario = Usuario::find($id);
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password); // Actualiza la contraseña solo si se proporciona una nueva
        }
        $usuario->institucion_id = $request->institucion_id;
        $usuario->departamento_id = $request->departamento_id;
        $usuario->tipo = $request->tipo;
        $usuario->remember_token = $request->has('remember') ? $request->remember : null; // Manejo del token de recordatorio
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
