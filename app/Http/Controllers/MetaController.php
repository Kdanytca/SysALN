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
            ->where('idEncargadoMeta', $usuario->nombre_usuario)
            ->get();

        if ($metas->isEmpty()) {
            return redirect()->route('home')->with('error', 'No tienes metas asignadas.');
        }

        // Obtener el plan del primer meta para pasarlo a la vista
        $plan = $metas->first()->planEstrategico ?? null;

        $usuarios = Usuario::all();

        return view('metas.index', [
            'metas' => $metas,
            'usuarios' => $usuarios,
            'plan' => $plan,
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
            'idEncargadoMeta' => $request->idEncargadoMeta,
            'nombre_meta' => $request->nombre_meta,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'nombre_actividades' => implode(',', $request->nombre_actividades),
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
            'idEncargadoMeta' => $request->idEncargadoMeta,
            'nombre_meta' => $request->nombre_meta,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'nombre_actividades' => implode(',', $request->nombre_actividades),
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
