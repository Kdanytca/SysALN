<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    // Muestra la lista de instituciones
    public function index()
    {
        $instituciones = Institucion::all();
        return view('instituciones.index',['instituciones' => $instituciones]);
    }

    // Se encarga de crear una nueva institución
    public function store(Request $request)
    {
        request()->validate([
            'nombre_institucion' => 'required|string|max:255',
            'tipo_institucion' => 'required|string|max:255',
            'encargado_proyecto' => 'required|string|max:255',
        ]);

        $institucion = new Institucion();
        $institucion->nombre_institucion = $request->nombre_institucion;
        $institucion->tipo_institucion = $request->tipo_institucion;
        $institucion->encargado_proyecto = $request->encargado_proyecto;
        $institucion->save();

        return redirect()->route('instituciones.index')->with('success', 'Institución registrada correctamente.');
    }

    // se encarga de editar una institución
    public function update(Request $request, string $id)
    {
        request()->validate([
            'nombre_institucion' => 'required|string|max:255',
            'tipo_institucion' => 'required|string|max:255',
            'encargado_proyecto' => 'required|string|max:255',
        ]);

        $institucion = Institucion::find($id);
        $institucion->nombre_institucion = $request->nombre_institucion;
        $institucion->tipo_institucion = $request->tipo_institucion;
        $institucion->encargado_proyecto = $request->encargado_proyecto;
        $institucion->save();

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
