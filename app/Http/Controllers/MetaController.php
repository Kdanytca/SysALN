<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\PlanEstrategico;
use App\Models\Usuario;
use App\Models\Institucion;
use App\Models\Departamento;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MetaController extends Controller
{
    public function index()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $metas = Meta::with('planEstrategico')->get();
        $planes = PlanEstrategico::all();
        $usuarios = Usuario::all(); // Usar el modelo correcto 'Usuario'

        $vistaMetas = true;

        return view('metas.index', compact('metas', 'planes', 'usuarios', 'vistaMetas'));
    }


    // Vista de metas por plan (solo admin y encargados)
    public function indexPorPlan(PlanEstrategico $plan)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $plan->load('departamento.institucion');
        $metas = $plan->metas()->with('planEstrategico')->get();
        $planes = PlanEstrategico::all();

        $usuarios = Usuario::where('idInstitucion', $plan->departamento->idInstitucion)
            ->whereIn('tipo_usuario', ['responsable_meta', 'encargado_institucion'])
            ->get();

        $departamentos = Departamento::all();
        
        $institucion = $plan->departamento->institucion;

        $vistaMetas = true;

        return view('metas.index', compact('metas', 'plan', 'planes', 'usuarios', 'institucion', 'departamentos', 'vistaMetas'));
    }

    // Vista exclusiva para el responsable_meta
    public function indexResponsable()
    {
        $usuario = Auth::user();

        if ($usuario->tipo_usuario !== 'responsable_meta') {
            return redirect()->route('home')->with('error', 'No tienes permiso para ver esta sección.');
        }

        $metas = Meta::with(['planEstrategico.departamento.institucion'])
            ->where('idEncargadoMeta', $usuario->id)
            ->get();

        if ($metas->isEmpty()) {
            return redirect()->route('home')->with('error', 'No tienes metas asignadas.');
        }

        // Obtener el plan del primer meta para pasarlo a la vista
        $plan = $metas->first()->planEstrategico ?? null;

        $usuarios = Usuario::all();

        $institucion = $plan->departamento->institucion;

        return view('metas.index', [
            'metas' => $metas,
            'usuarios' => $usuarios,
            'plan' => $plan,
            'institucion' => $institucion,
        ]);
    }


    // Crear meta (solo admin y encargados)
    public function create($planId)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear metas.');
        }

        $plan = PlanEstrategico::findOrFail($planId);
        $usuarios = Usuario::where('idDepartamento', $plan->idDepartamento)->get();

        return view('metas.create', compact('plan', 'usuarios'));
    }


    public function store(Request $request)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear metas.');
        }

        $plan = PlanEstrategico::findOrFail($request->idPlanEstrategico);

        $request->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'idEncargadoMeta' => 'required|exists:usuarios,id',
            'tipo' => 'required|in:meta,estrategia',
            'nombre' => 'required|string',
            'objetivos_estrategias' => 'nullable|array|min:1',
            'objetivos_estrategias.*' => 'nullable|string',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string',
            'nombre_actividades' => 'required|array|min:1',
            'nombre_actividades.*' => 'required|string',
            'resultados_esperados' => 'nullable|string',
            'indicador_resultados' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string',
        ]);

        if ($request->fecha_inicio < $plan->fecha_inicio) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio de la meta no puede ser anterior a la del plan.']);
        }

        if ($request->fecha_fin > $plan->fecha_fin) {
            return back()->withErrors(['fecha_fin' => 'La fecha de fin de la meta no puede ser posterior a la del plan.']);
        }

        Meta::create([
            'idPlanEstrategico' => $request->idPlanEstrategico,
            'idEncargadoMeta' => $request->idEncargadoMeta,
            'tipo' => $request->tipo,
            'nombre' => $request->nombre,
            'objetivos_estrategias' => json_encode($request->objetivos_estrategias),
            'ejes_estrategicos' => json_encode($request->ejes_estrategicos),
            'nombre_actividades' => json_encode($request->nombre_actividades),
            'resultados_esperados' => $request->resultados_esperados,
            'indicador_resultados' => $request->indicador_resultados,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'comentario' => $request->comentario,
        ]);

        return redirect()->route('plan.metas', $request->idPlanEstrategico)
            ->with('success', 'Meta creada exitosamente.');
    }


    // Editar meta (solo admin y encargados)
    public function update(Request $request, Meta $meta)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para editar metas.');
        }

        $plan = PlanEstrategico::findOrFail($request->idPlanEstrategico);

        $request->validate([
            'idPlanEstrategico' => 'required|exists:planes_estrategicos,id',
            'idEncargadoMeta' => 'required|exists:usuarios,id',
            'tipo' => 'required|in:meta,estrategia',
            'nombre' => 'required|string',
            'objetivos_estrategias' => 'nullable|array|min:1',
            'objetivos_estrategias.*' => 'nullable|string',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string',
            'nombre_actividades' => 'required|array|min:1',
            'nombre_actividades.*' => 'required|string',
            'resultados_esperados' => 'nullable|string',
            'indicador_resultados' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'comentario' => 'nullable|string',
        ]);

        if ($request->fecha_inicio < $plan->fecha_inicio) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio de la meta no puede ser anterior a la del plan.']);
        }
        if ($request->fecha_fin > $plan->fecha_fin) {
            return back()->withErrors(['fecha_fin' => 'La fecha de fin de la meta no puede ser posterior a la del plan.']);
        }
        
        $meta->update([
            'idPlanEstrategico' => $request->idPlanEstrategico,
            'idEncargadoMeta' => $request->idEncargadoMeta,
            'tipo' => $request->tipo,
            'nombre' => $request->nombre,
            'objetivos_estrategias' => json_encode($request->objetivos_estrategias),
            'ejes_estrategicos' => json_encode($request->ejes_estrategicos),
            'nombre_actividades' => json_encode($request->nombre_actividades),
            'resultados_esperados' => $request->resultados_esperados,
            'indicador_resultados' => $request->indicador_resultados,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', 'Meta actualizada exitosamente.');
    }

    // Eliminar meta (solo admin y encargados)
    public function destroy(Meta $meta)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar metas.');
        }

        $meta->delete();

        return redirect()->back()->with('success', 'Meta eliminada exitosamente.');
    }

    public function createDesdePlan(PlanEstrategico $plan)
    {
        return view('metas.create', compact('plan'));
    }
}
