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
    /**
     * Mostrar formulario de creación de Plan Estratégico.
     */
    public function index()
    {
        $instituciones = Institucion::with('departamentos')->get();
        $planes = PlanEstrategico::with(['departamento', 'responsable'])->get();
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();

        return view('planes.index', compact('planes', 'departamentos', 'usuarios', 'instituciones'));
    }
    /**
     * Mostrar los planes estratégicos de una institución específica.
     */
    // PlanController.php
    public function planesPorInstitucion($id)
    {
        $institucion = Institucion::with('departamentos')->findOrFail($id);

        $planes = PlanEstrategico::whereHas('departamento', function ($q) use ($id) {
            $q->where('idInstitucion', $id);
        })->with(['departamento', 'responsable'])->get();

        // Obtener usuarios de los departamentos de esa institución
        $usuariosDisponibles = Usuario::whereHas('departamento', function ($q) use ($id) {
            $q->where('idInstitucion', $id);
        })->get();

        return view('planes.por_institucion', compact('institucion', 'planes', 'usuariosDisponibles'));
    }



    /**
     * Guardar el nuevo Plan Estratégico.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'metas' => 'nullable|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'indicador' => 'nullable|string|max:45',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        PlanEstrategico::create([
            'idDepartamento' => $request->idDepartamento,
            'idUsuario' => $request->responsable,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'metas' => $request->metas ?? '',
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'indicador' => '',
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('institucion.planes', [
            'id' => $request->institucion_id
        ]);
    }


    //eliminar
    public function destroy($id)
    {
        $plan = PlanEstrategico::findOrFail($id);
        $institucion_id = $plan->departamento->idInstitucion;
        $plan->delete();

        return redirect()->route('institucion.planes', [
            'id' => $institucion_id
        ])->with('success', 'Plan eliminado.');
    }

    //actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|array|min:1',
            'ejes_estrategicos.*' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        $plan = PlanEstrategico::findOrFail($id);
        $plan->update([
            'idDepartamento' => $request->idDepartamento,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'ejes_estrategicos' => implode(',', $request->ejes_estrategicos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'idUsuario' => $request->responsable,
        ]);

        $institucion_id = $plan->departamento->idInstitucion;

        // Actualizar los ejes en todas las metas relacionadas
        DB::table('metas')
            ->where('idPlanEstrategico', $plan->id)
            ->update(['ejes_estrategicos' => $plan->ejes_estrategicos]);
        
        return redirect()->route('institucion.planes', [
            'id' => $institucion_id
        ])->with('success', 'Plan actualizado correctamente');
    }

    //Ver planes globales
    public function indexGlobal()
    {
        $planes = PlanEstrategico::with([
            'departamento.institucion',
            'responsable'
        ])->get();

        return view('planes.index', compact('planes'));
    }
}
