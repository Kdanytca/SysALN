<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Institucion;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    // Muestra la lista de departamentos
    public function index()
    {
        $departamentos = Departamento::with('institucion')->get();
        $instituciones = Institucion::all();

        return view('departamentos.index', compact('departamentos', 'instituciones'));
    }

    // Muestra la lista de departamentos filtrados por institución
    public function indexPorInstitucion(Institucion $institucion)
    {
        $departamentos = $institucion->departamentos()->with('institucion')->get();
        $instituciones = Institucion::all();

        return view('departamentos.index', compact('departamentos', 'institucion', 'instituciones'));
    }

    // Se encarga de crear un nuevo departamento
    public function create()
    {
        $instituciones = Institucion::all();
        return view('departamentos.create', compact('instituciones'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'departamento' => 'required|string|max:255',
            'encargado_departamento' => 'required|string|max:45',
            'idInstitucion' => 'required|exists:instituciones,id',
        ]);

        $departamento = new Departamento();
        $departamento->departamento = $request->departamento;
        $departamento->encargado_departamento = $request->encargado_departamento;
        $departamento->idInstitucion = $request->idInstitucion;
        $departamento->save();

        return redirect()->back()->with('success', 'Departamento creado exitosamente.');
    }

    //mostrando todos los departamentos
    public function todos()
    {
        $departamentos = Departamento::with('institucion')->get(); // si tienes relación con Institución
        return view('departamentos.index_general', compact('departamentos'));
    }

    // Se encarga de editar un departamento
    public function edit(string $id)
    {
        $departamento = Departamento::find($id);
        $instituciones = Institucion::all();
        return view('departamentos.edit', compact('departamento', 'instituciones'));
    }

    public function update(Request $request, string $id)
    {
        request()->validate([
            'departamento' => 'required|string|max:255',
            'encargado_departamento' => 'required|string|max:45',
            'idInstitucion' => 'required|exists:instituciones,id',
        ]);

        $departamento = Departamento::find($id);
        $departamento->departamento = $request->departamento;
        $departamento->encargado_departamento = $request->encargado_departamento;
        $departamento->idInstitucion = $request->idInstitucion;
        $departamento->save();

        return redirect()->back()->with('success', 'Departamento actualizado correctamente.');
    }

    // Elimina un departamento
    public function destroy(string $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return redirect()->back()->with('success', 'Departamento eliminado correctamente.');
    }
}
