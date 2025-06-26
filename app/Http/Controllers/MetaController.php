<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metas = Meta::with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        return view('metas.index', compact('metas', 'planes'));
    }

    public function indexPorPlan(PlanEstrategico $plan)
    {
        $metas = $plan->metas()->with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        return view('metas.index', compact('metas', 'plan', 'planes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $planes = PlanEstrategico::all();
        return view('metas.create', compact('planes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'usuario_responsable' => 'required|string|max:255',
            'nombre_meta' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|string|max:255',
            'actividades' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string|max:255',
        ]);

        $meta = new Meta();
        $meta->idPlanEstrategico = $request->idPlanEstrategico;
        $meta->usuario_responsable = $request->usuario_responsable;
        $meta->nombre_meta = $request->nombre_meta;
        $meta->ejes_estrategicos = $request->ejes_estrategicos;
        $meta->actividades = $request->actividades;
        $meta->fecha_inicio = $request->fecha_inicio;
        $meta->fecha_fin = $request->fecha_fin;
        $meta->comentario = $request->comentario;
        $meta->save();

        return redirect()->back()->with('success', 'Meta creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meta $meta)
    {
        $meta = Meta::findOrFail($meta->id);
        $planes = PlanEstrategico::all();
        return view('metas.edit', compact('meta', 'planes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meta $meta)
    {
        request()->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'usuario_responsable' => 'required|string|max:255',
            'nombre_meta' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|string|max:255',
            'actividades' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string|max:255',
        ]);

        $meta->update($request->all());

        return redirect()->back()->with('success', 'Meta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meta $meta)
    {
        $meta = Meta::findOrFail($meta->id);
        $meta->delete();

        return redirect()->back()->with('success', 'Meta eliminada exitosamente.');
    }
}
