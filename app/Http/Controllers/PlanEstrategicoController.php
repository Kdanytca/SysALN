<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Departamento;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlanEstrategicoController extends Controller
{
    // Vista general solo para admins o encargados institucionales
    public function index()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $instituciones = Institucion::with('departamentos')->get();
        $planes = PlanEstrategico::with(['departamento', 'responsable'])->get();
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();

        return view('planes.index', compact('planes', 'departamentos', 'usuarios', 'instituciones'));
    }

    // Vista de planes por institución (solo admins y encargados)
    public function planesPorInstitucion($id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $institucion = Institucion::with('departamentos')->findOrFail($id);

        $planes = PlanEstrategico::whereHas('departamento', function ($q) use ($id) {
            $q->where('idInstitucion', $id);
        })->with(['departamento', 'responsable'])->get();

        $usuariosDisponibles = Usuario::whereHas('departamento', function ($q) use ($id) {
            $q->where('idInstitucion', $id);
        })->get();

        return view('planes.por_institucion', compact('institucion', 'planes', 'usuariosDisponibles'));
    }

    // Vista exclusiva para el responsable del plan asignado
    public function verResponsable()
    {
        $usuario = Auth::user();

        if ($usuario->tipo_usuario !== 'responsable_plan') {
            return redirect()->route('home')->with('error', 'No tienes permiso para ver este plan.');
        }

        $plan = PlanEstrategico::with(['departamento.institucion', 'responsable'])
            ->where('idUsuario', $usuario->id)
            ->get();  // colección, no solo 1 plan

        if ($plan->isEmpty()) {
            return redirect()->route('home')->with('error', 'No tienes un plan asignado.');
        }

        return view('planes.index', ['planes' => $plan]);
    }




    // Crear plan (solo admin y encargado_institucion)
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear planes.');
        }

        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'metas' => 'nullable|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:100',
            'objetivos' => 'nullable|array',
            'objetivos.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'indicador' => 'nullable|string|max:45',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        $plan = PlanEstrategico::create([
            'idDepartamento' => $request->idDepartamento,
            'idUsuario' => $request->responsable,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'metas' => $request->metas ?? '',
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'objetivos' => $request->objetivos ? json_encode($request->objetivos) : json_encode([]),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'indicador' => '',
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('institucion.planes', [
            'id' => $request->institucion_id
        ])->with('success', 'Plan registrado correctamente.');
    }


    // Actualizar
    public function update(Request $request, $id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso para editar planes.');
        }

        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:100',
            'objetivos' => 'nullable|array',
            'objetivos.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        $plan = PlanEstrategico::findOrFail($id);

        $plan->update([
            'idDepartamento' => $request->idDepartamento,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'objetivos' => $request->objetivos ? json_encode($request->objetivos) : null,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'idUsuario' => $request->responsable,
        ]);

        $institucion_id = $plan->departamento->idInstitucion;

        return redirect()->route('institucion.planes', [
            'id' => $institucion_id
        ])->with('success', 'Plan actualizado correctamente.');
    }

    // Eliminar
    public function destroy($id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar planes.');
        }

        $plan = PlanEstrategico::findOrFail($id);
        $institucion_id = $plan->departamento->idInstitucion;
        $plan->delete();

        return redirect()->route('institucion.planes', [
            'id' => $institucion_id
        ])->with('success', 'Plan eliminado correctamente.');
    }

    // Para administrador ver todos los planes
    public function indexGlobal()
    {
        if (Auth::user()->tipo_usuario !== 'administrador') {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $planes = PlanEstrategico::with(['departamento.institucion', 'responsable'])->get();

        return view('planes.index', compact('planes'));
    }

    // cambiar estado del plan a finalizado
    public function toggleFinalizar($id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan'])) {
            return redirect()->back()->with('error', 'No tienes permiso para cambiar el estado del plan.');
        }

        $plan = PlanEstrategico::findOrFail($id);

        if ($plan->indicador === 'finalizado') {
            // Reanudar el plan → dejarlo como 'activo'
            $plan->indicador = 'activo';
        } else {
            // Finalizar el plan
            $plan->indicador = 'finalizado';
        }

        $plan->save();

        return back()->with('status', 'Estado del plan actualizado.');
    }
}
