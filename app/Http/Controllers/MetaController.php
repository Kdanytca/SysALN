<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\PlanEstrategico;
use App\Models\Usuario;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    // Muestra la lista de metas
    public function index()
    {
        $metas = Meta::with('planEstrategico')->get();
        $planes = PlanEstrategico::all();
        $usuarios = Usuario::all();

        return view('metas.index', compact('metas', 'planes', 'usuarios'));
    }

    // Muestra la lista de metas filtradas por plan estratégico
    public function indexPorPlan(PlanEstrategico $plan)
    {
        $plan->load('departamento.institucion'); // Esto incluye la institución relacionada
        $metas = $plan->metas()->with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        // Obtener solo los usuarios del mismo departamento del plan
        $usuarios = Usuario::where('idDepartamento', $plan->idDepartamento)->get();

        return view('metas.index', compact('metas', 'plan', 'planes', 'usuarios'));
    }

    // Filtra el usuario responsable
    public function create($planId)
    {
        $plan = PlanEstrategico::findOrFail($planId);

        // Obtener solo usuarios del mismo departamento del plan
        $usuarios = Usuario::where('idDepartamento', $plan->idDepartamento)->get();

        return view('metas.create', compact('plan', 'usuarios'));
    }

    // Se encarga de crear una nueva meta
    public function store(Request $request)
    {
        $plan = PlanEstrategico::findOrFail($request->idPlanEstrategico);

        $request->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'usuario_responsable' => [
                'required',
                Rule::exists('usuarios', 'nombre_usuario')->where(function ($query) use ($plan) {
                    return $query->where('idDepartamento', $plan->idDepartamento);
                }),
            ],
            'nombre_meta' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:255',
            'nombre_actividades' => 'required|array|min:1',
            'nombre_actividades.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string|max:255',
        ]);

        Meta::create([
            'idPlanEstrategico' => $request->idPlanEstrategico,
            'usuario_responsable' => $request->usuario_responsable,
            'nombre_meta' => $request->nombre_meta,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'nombre_actividades' => implode(',', $request->nombre_actividades),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', 'Meta creada exitosamente.');
    }

    // Se encarga de editar una meta
    public function update(Request $request, Meta $meta)
    {
        $plan = PlanEstrategico::findOrFail($request->idPlanEstrategico);

        $request->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'usuario_responsable' => [
                'required',
                Rule::exists('usuarios', 'nombre_usuario')->where(function ($query) use ($plan) {
                    return $query->where('idDepartamento', $plan->idDepartamento);
                }),
            ],
            'nombre_meta' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:255',
            'nombre_actividades' => 'required|array|min:1',
            'nombre_actividades.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string|max:255',
        ]);

        $meta->update([
            'idPlanEstrategico' => $request->idPlanEstrategico,
            'usuario_responsable' => $request->usuario_responsable,
            'nombre_meta' => $request->nombre_meta,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'nombre_actividades' => implode(',', $request->nombre_actividades),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'comentario' => $request->comentario,
        ]);

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
