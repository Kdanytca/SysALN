<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actividades = Actividad::with('meta', 'usuario')->get();
        $metas = Meta::all();
        $usuarios = Usuario::all();
        return view('actividades.index', compact('actividades', 'metas', 'usuarios'));
    }

    public function indexPorMeta(Meta $meta)
    {
        $actividades = $meta->actividades()->with('meta')->get();
        $metas = Meta::all();
        $usuarios = Usuario::all();
        return view('actividades.index', compact('actividades', 'meta', 'metas', 'usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $metas = Meta::all();
        $usuarios = Usuario::all();
        return view('actividades.create', compact('metas', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idUsuario' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'required|string|max:255',
        ]);

        Actividad::create($validated);

        return redirect()->back()->with('success', 'Actividad creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Actividad $actividad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Actividad $actividad)
    {
        $actividad = Actividad::find($actividad->id);
        $metas = Meta::all();
        $usuarios = Usuario::all();
        return view('actividades.edit', compact('actividad', 'metas', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Actividad $actividad)
    {
        $validated = $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idUsuario' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'required|string|max:255',
        ]);

        $actividad->update($validated);

        return redirect()->back()->with('success', 'Actividad actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Actividad $actividad)
    {
        $actividad = Actividad::findOrFail($actividad->id);
        $actividad->delete();
        return redirect()->back()->with('success', 'Actividad eliminada exitosamente.');
    }
}
