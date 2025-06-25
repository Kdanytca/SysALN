<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Departamento;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanEstrategicoController extends Controller
{
    /**
     * Mostrar formulario de creación de Plan Estratégico.
     */
    public function create($id)
    {
        $departamentos = Departamento::all();
        $institucion = Institucion::with('departamentos')->findOrFail($id);

        // Obtener IDs de los departamentos de esa institución
        $departamentoIds = $institucion->departamentos->pluck('id');

        // Filtrar usuarios que pertenezcan a esos departamentos y que no estén ya asignados a un plan
        $usuariosDisponibles = Usuario::whereIn('idDepartamento', $departamentoIds)
            ->whereDoesntHave('planesEstrategicos') // usuario no está en ningún plan
            ->get();

        return view('planes.create', compact('institucion', 'usuariosDisponibles', 'departamentos'));
    }

    public function index()
    {
        $instituciones = Institucion::with('departamentos')->get();
        $planes = PlanEstrategico::with(['departamento', 'responsable'])->get();
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();

        return view('planes.index', compact('planes', 'departamentos', 'usuarios', 'instituciones'));
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
            'ejes_estrategicos' => 'required|string|max:255',
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
            'ejes_estrategicos' => $request->ejes_estrategicos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'indicador' => '',
            'creado_por' => Auth::id(),
        ]);

        // Redirección según el origen del formulario
        if ($request->origen === 'planes_index') {
            return redirect()->route('planes.index')->with('success', 'Plan Estratégico creado correctamente.');
        }

        return redirect()->route('instituciones.index')->with('success', 'Plan Estratégico creado correctamente.');
    }


    //eliminar
    public function destroy($id)
    {
        $plan = PlanEstrategico::findOrFail($id);
        $plan->delete();

        return redirect()->route('planes.index')->with('success', 'Plan eliminado correctamente.');
    }
    //actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'ejes_estrategicos' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        $plan = PlanEstrategico::findOrFail($id);
        $plan->update([
            'idDepartamento' => $request->idDepartamento,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'ejes_estrategicos' => $request->ejes_estrategicos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'idUsuario' => $request->responsable,
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan actualizado correctamente');
    }
}
