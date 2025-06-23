<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Institucion;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departamentos = Departamento::with('instituciones')->get();
        $instituciones = Institucion::all();
        return view('departamentos.index',compact('departamentos', 'instituciones'));
    }

    public function indexPorInstitucion(Institucion $institucion)
    {
        $departamentos = $institucion->departamentos()->with('instituciones')->get();
        $instituciones = Institucion::all();

        return view('departamentos.index', compact('departamentos', 'institucion', 'instituciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instituciones = Institucion::all();
        return view('departamentos.create', compact('instituciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'nombre' => 'required|string|max:255',
            'institucion_id' => 'required|exists:instituciones,id',
        ]);

        $departamento = new Departamento();
        $departamento->nombre = $request->nombre;
        $departamento->institucion_id = $request->institucion_id;
        $departamento->save();

        return redirect()->back()->with('success', 'Departamento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Departamento $departamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $departamento = Departamento::find($id);
        $instituciones = Institucion::all();
        return view('departamentos.edit', ['departamento' => $departamento], compact('instituciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'nombre' => 'required|string|max:255',
            'institucion_id' => 'required|exists:instituciones,id',
        ]);

        $departamento = Departamento::find($id);
        $departamento->nombre = $request->nombre;
        $departamento->institucion_id = $request->institucion_id;
        $departamento->save();

        return redirect()->back()->with('success', 'Departamento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return redirect()->back()->with('success', 'Departamento eliminado correctamente.');
    }
}
