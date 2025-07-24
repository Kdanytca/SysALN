<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstitucionController extends Controller
{
    // Muestra la lista de instituciones
    public function index()
    {
        $instituciones = Institucion::with('encargadoInstitucion')->get();
        $usuarios = Usuario::all();

        // Obtener usuarios tipo 'encargado_institucion' que aún no han sido asignados como encargados
        $usuariosParaCrear = Usuario::where('tipo_usuario', 'encargado_institucion')
            ->whereDoesntHave('instituciones') // Solo los libres
            ->get();

        $usuariosParaEditar = Usuario::where('tipo_usuario', 'encargado_institucion')->get();

        return view('instituciones.index', compact('instituciones', 'usuarios', 'usuariosParaCrear', 'usuariosParaEditar'));
    }

    // Se encarga de crear una nueva institución
    public function store(Request $request)
    {
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

        // Asignar esa institución al usuario encargado
        Usuario::where('id', $request->idEncargadoInstitucion)
            ->update(['idInstitucion' => $institucion->id]);

        return redirect()->route('instituciones.index')->with('success', 'Institución registrada correctamente.');
    }

    // se encarga de editar una institución
    public function update(Request $request, string $id)
    {
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
    
    // Elimina una institución
    public function destroy($id)
    {
        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente.');
    }
}
