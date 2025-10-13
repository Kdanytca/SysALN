<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Departamento;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BackupPlan;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Traemos la institución con sus departamentos y usuarios
        $institucion = Institucion::with('departamentos')->findOrFail($id);

        // Ahora, gracias a la relación hasManyThrough en el modelo Institucion
        // podemos acceder a todos los planes de esa institución
        $planes = $institucion->planes()->with(['departamento', 'responsable'])->get();

        // Usuarios disponibles de la institución (para asignar a planes)
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

    // Crear backup
    public function backup($id)
    {
        $plan = PlanEstrategico::with(['metas.actividades.seguimientos', 'departamento', 'responsable'])->findOrFail($id);

        if (BackupPlan::where('idPlanOriginal', $plan->id)->exists()) {
            return back()->with('error', 'Ya existe un backup de este plan.');
        }

        $metasBackup = $plan->metas->map(function ($meta) {
            return [
                'id' => $meta->id,
                'nombre' => $meta->nombre,
                'ejes_estrategicos' => $meta->ejes_estrategicos,
                'responsable' => $meta->responsable ? $meta->responsable->nombre_usuario : null,
                'resultados_esperados' => $meta->resultados_esperados,
                'indicador_resultados' => $meta->indicador_resultados,
                'fecha_inicio' => $meta->fecha_inicio,
                'fecha_fin' => $meta->fecha_fin,
                'comentario' => $meta->comentario,
                'actividades' => $meta->actividades->map(function ($actividad) {
                    return [
                        'id' => $actividad->id,
                        'nombre_actividad' => $actividad->nombre_actividad,
                        'objetivos' => $actividad->objetivos,
                        'encargado' => $actividad->encargadoActividad ? $actividad->encargadoActividad->nombre_usuario : null,
                        'fecha_inicio' => $actividad->fecha_inicio,
                        'fecha_fin' => $actividad->fecha_fin,
                        'comentario' => $actividad->comentario,
                        'unidad_encargada' => $actividad->unidad_encargada,
                        'evidencia' => $actividad->evidencia,
                        'seguimientos' => $actividad->seguimientos->map(function ($seguimiento) {
                            return [
                                'id' => $seguimiento->id,
                                'periodo_consultar' => $seguimiento->periodo_consultar,
                                'observaciones' => $seguimiento->observaciones,
                                'estado' => $seguimiento->estado,
                                'documento' => $seguimiento->documento,
                            ];
                        }),
                    ];
                }),
            ];
        });

        BackupPlan::create([
            'idPlanOriginal' => $plan->id,
            'idDepartamento' => $plan->idDepartamento,
            'idUsuario' => $plan->idUsuario,
            'nombre_plan_estrategico' => $plan->nombre_plan_estrategico,
            'nombre_departamento' => $plan->departamento->departamento ?? null,
            'nombre_responsable' => $plan->responsable->nombre_usuario ?? null,
            'metas' => json_encode($metasBackup),
            'ejes_estrategicos' => $plan->ejes_estrategicos,
            'objetivos' => $plan->objetivos,
            'fecha_inicio' => $plan->fecha_inicio,
            'fecha_fin' => $plan->fecha_fin,
            'indicador' => $plan->indicador,
            'creado_por' => Auth::id(),
        ]);

        return back()->with('success', 'Backup completo creado correctamente.');
    }

    // Ver backup
    public function verBackup($id)
    {
        $backup = BackupPlan::findOrFail($id);
        return view('planes.ver_backup', compact('backup'));
    }

    // Index de backups
    public function respaldoIndex()
    {
        $backups = BackupPlan::all();
        return view('planes.backup_index', compact('backups'));
    }

    public function eliminarConUsuarios($id)
    {
        // 1. Obtener el plan con la relación de departamento e institución
        $plan = PlanEstrategico::with('departamento.institucion')->findOrFail($id);

        // 2. Buscar usuarios relacionados con metas
        $usuariosMetas = DB::table('metas')
            ->where('idPlanEstrategico', $id)
            ->whereNotNull('idEncargadoMeta')
            ->pluck('idEncargadoMeta');

        // 3. Buscar usuarios relacionados con actividades
        $usuariosActividades = DB::table('actividades')
            ->join('metas', 'actividades.idMetas', '=', 'metas.id')
            ->where('metas.idPlanEstrategico', $id)
            ->whereNotNull('actividades.idEncargadoActividad')
            ->pluck('actividades.idEncargadoActividad');

        // 4. Unir usuarios únicos relacionados
        $usuariosRelacionados = $usuariosMetas->merge($usuariosActividades)->unique();

        // 5. Obtener usuarios a eliminar (excepto los de tipo encargado_institucion)
        $usuariosAEliminar = DB::table('usuarios')
            ->whereIn('id', $usuariosRelacionados)
            ->where('tipo_usuario', '!=', 'encargado_institucion')
            ->pluck('id');

        // 6. Eliminar los usuarios filtrados
        DB::table('usuarios')->whereIn('id', $usuariosAEliminar)->delete();

        // 7. Eliminar el plan (esto eliminará metas, actividades y resultados por cascada)
        $plan->delete();

        $institucionId = $plan->departamento->institucion->id;

        return redirect()->route('institucion.planes', $institucionId)
            ->with('success', 'Plan eliminado correctamente.');
    }
    // Descargar backup en PDF
    public function descargarBackup($id)
    {
        $backup = BackupPlan::findOrFail($id);

        // Carga la vista que ya usas para mostrarlo, o una nueva adaptada a PDF
        $pdf = Pdf::loadView('planes.ver_backup_pdf', compact('backup'));

        // Nombre del archivo de salida
        $nombreArchivo = 'Respaldo_' . preg_replace('/\s+/', '_', $backup->nombre_plan_estrategico) . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
