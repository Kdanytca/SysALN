<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instituciones = Institucion::all();
        return view('instituciones.index',['instituciones' => $instituciones]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('instituciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Institucion $institucion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $institucion = Institucion::find($id);
        return view('instituciones.edit', ['institucion' => $institucion]);
    }

    /**
     * Update the specified resource in storage.
     */
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
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente.');
    }
}
