<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\Usuario;
use App\models\Departamento;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    // Muestra la lista de actividades en el index
    public function index()
    {
        $actividades = Actividad::with('meta', 'usuario')->get();
        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();
        return view('actividades.index', compact('actividades', 'metas', 'usuarios', 'departamentos'));
    }

    // Muestra la lista de actividades filtradas por meta
    public function indexPorMeta(Meta $meta)
    {
        $meta->load('planEstrategico');
        $actividades = $meta->actividades()->with('meta')->get();
        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();
        return view('actividades.index', compact('actividades', 'meta', 'metas', 'usuarios', 'departamentos'));
    }

    // Se encarga de crear una nueva actividad
    public function create()
    {
        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();
        return view('actividades.create', compact('metas', 'usuarios', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idUsuario' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'required|string|max:255',
        ]);

        Actividad::create([
            'idMetas' => $request->idMetas,
            'idUsuario' => $request->idUsuario,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => $request->objetivos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'resultados_esperados' => $request->resultados_esperados,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad creada exitosamente.');
    }

    // se encarga de editar una actividad
    public function edit(Actividad $actividad)
    {
        $actividad = Actividad::find($actividad->id);
        $metas = Meta::all();
        $usuarios = Usuario::all();
        $usuarios = Usuario::with('departamento')->get();
        $departamentos = Departamento::all();
        return view('actividades.edit', compact('actividad', 'metas', 'usuarios', 'departamentos'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idUsuario' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'required|string|max:255',
        ]);

        $actividad = Actividad::find($id);
        $actividad->update([
            'idMetas' => $request->idMetas,
            'idUsuario' => $request->idUsuario,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => $request->objetivos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'resultados_esperados' => $request->resultados_esperados,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad actualizada exitosamente.');
    }

    // Elimina una actividad
    public function destroy(string $id)
    {
        $actividad = Actividad::findOrFail($id);
        $actividad->delete();
        return redirect()->back()->with('success', 'Actividad eliminada exitosamente.');
    }
}
