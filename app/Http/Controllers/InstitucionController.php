<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class InstitucionController extends Controller
{
    public function index()
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para ver esta sección.');
        }

        $instituciones = Institucion::with('encargadoInstitucion')->get();
        $usuarios = Usuario::all();

        $usuariosParaCrear = Usuario::where('tipo_usuario', 'encargado_institucion')
            ->whereDoesntHave('instituciones')
            ->get();

        $usuariosParaEditar = Usuario::where('tipo_usuario', 'encargado_institucion')->get();

        return view('instituciones.index', compact('instituciones', 'usuarios', 'usuariosParaCrear', 'usuariosParaEditar'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para crear instituciones.');
        }

        $request->validate([
            'nombre_institucion' => 'required|string|max:255',
            'tipo_institucion' => 'required|string|max:255',
            'idEncargadoInstitucion' => [
                'required',
                'exists:usuarios,id',
                Rule::unique('instituciones', 'idEncargadoInstitucion')
            ],
        ]);

        $institucion = Institucion::create([
            'nombre_institucion' => $request->nombre_institucion,
            'tipo_institucion' => $request->tipo_institucion,
            'idEncargadoInstitucion' => $request->idEncargadoInstitucion,
        ]);

        Usuario::where('id', $request->idEncargadoInstitucion)
            ->update(['idInstitucion' => $institucion->id]);

        return redirect()->route('instituciones.index')->with('success', 'Institución registrada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar instituciones.');
        }

        $request->validate([
            'nombre_institucion' => 'required|string|max:255',
            'tipo_institucion' => 'required|string|max:255',
            'idEncargadoInstitucion' => 'required|exists:usuarios,id',
        ]);

        $institucion = Institucion::find($id);
        $institucion->update([
            'nombre_institucion' => $request->nombre_institucion,
            'tipo_institucion' => $request->tipo_institucion,
            'idEncargadoInstitucion' => $request->idEncargadoInstitucion,
        ]);

        return redirect()->route('instituciones.index')->with('success', 'Institución actualizada correctamente.');
    }

    public function destroy($id)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar instituciones.');
        }

        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente.');
    }

    public function ver($id)
    {
        $usuario = Auth::user();

        if ($usuario->tipo_usuario !== 'encargado_institucion' || $usuario->idInstitucion != $id) {
            return redirect()->back()->with('error', 'No tienes permiso para ver esta institución.');
        }

        $instituciones = Institucion::where('id', $id)->get();
        $usuariosParaCrear = Usuario::all();
        $usuariosParaEditar = Usuario::all();

        return view('instituciones.index', compact('instituciones', 'usuariosParaCrear', 'usuariosParaEditar'));
    }
}
