<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    // Muestra la lista de metas
    public function index()
    {
        $metas = Meta::with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        return view('metas.index', compact('metas', 'planes'));
    }

    // Muestra la lista de metas filtradas por plan estratégico
    public function indexPorPlan(PlanEstrategico $plan)
    {
        $metas = $plan->metas()->with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        return view('metas.index', compact('metas', 'plan', 'planes'));
    }

    // Se encarga de crear una nueva meta
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

    // Se encarga de editar una meta
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

    // Elimina una meta
    public function destroy(Meta $meta)
    {
        $meta = Meta::findOrFail($meta->id);
        $meta->delete();

        return redirect()->back()->with('success', 'Meta eliminada exitosamente.');
    }
}
