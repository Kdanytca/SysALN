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
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'metas' => 'nullable|string|max:2000',
            'ejes_estrategicos' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        // ✅ Limpieza y normalización antes de guardar
        $ejes = $request->ejes_estrategicos;

        if (is_string($ejes)) {
            $ejes = trim($ejes, "[]\"'");
            $ejes = str_replace(['•', "\r", "\n"], "\n", $ejes);
            $ejes = array_filter(array_map('trim', preg_split('/[\r\n]+/', $ejes)));
        }

        if (is_array($ejes)) {
            $ejes = array_filter(array_map('trim', $ejes));
        }

        // ✅ Guardar solo el JSON limpio
        $plan = PlanEstrategico::create([
            'idDepartamento' => $request->idDepartamento,
            'idUsuario' => $request->responsable,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'metas' => $request->metas ?? '',
            'ejes_estrategicos' => json_encode(array_values($ejes), JSON_UNESCAPED_UNICODE),
            'objetivos' => json_encode($request->objetivos ?? [], JSON_UNESCAPED_UNICODE),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'indicador' => '',
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('institucion.planes', [
            'id' => $request->institucion_id
        ])->with('success', 'Plan registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion'])) {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        $request->validate([
            'idDepartamento' => 'required|exists:departamentos,id',
            'nombre_plan_estrategico' => 'required|string|max:255',
            'ejes_estrategicos' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'responsable' => 'required|exists:usuarios,id',
        ]);

        $plan = PlanEstrategico::findOrFail($id);

        // ✅ Limpieza y normalización antes de actualizar
        $ejes = $request->ejes_estrategicos;

        if (is_string($ejes)) {
            $ejes = trim($ejes, "[]\"'");
            $ejes = str_replace(['•', "\r", "\n"], "\n", $ejes);
            $ejes = array_filter(array_map('trim', preg_split('/[\r\n]+/', $ejes)));
        }

        if (is_array($ejes)) {
            $ejes = array_filter(array_map('trim', $ejes));
        }

        $plan->update([
            'idDepartamento' => $request->idDepartamento,
            'nombre_plan_estrategico' => $request->nombre_plan_estrategico,
            'ejes_estrategicos' => json_encode(array_values($ejes), JSON_UNESCAPED_UNICODE),
            'objetivos' => json_encode($request->objetivos ?? [], JSON_UNESCAPED_UNICODE),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'idUsuario' => $request->responsable,
        ]);

        return redirect()->route('institucion.planes', [
            'id' => $plan->departamento->idInstitucion
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

        // ✅ Decodificar y normalizar los ejes del plan
        $ejesPlanRaw = json_decode($plan->ejes_estrategicos, true);
        if (is_null($ejesPlanRaw)) {
            $ejesPlanRaw = $plan->ejes_estrategicos; // por si no era JSON
        }
        $ejesPlan = $this->normalizarEjes($ejesPlanRaw);

        // ✅ Procesar las metas
        $metasBackup = $plan->metas->map(function ($meta) {
            // Decodificar y normalizar los ejes de la meta
            $ejesMetaRaw = json_decode($meta->ejes_estrategicos, true);
            if (is_null($ejesMetaRaw)) {
                $ejesMetaRaw = $meta->ejes_estrategicos;
            }
            $ejesMeta = app(self::class)->normalizarEjes($ejesMetaRaw);

            return [
                'id' => $meta->id,
                'nombre' => $meta->nombre,
                'ejes_estrategicos' => json_encode($ejesMeta, JSON_UNESCAPED_UNICODE),
                'responsable' => $meta->responsable ? $meta->responsable->nombre_usuario : null,
                'resultados_esperados' => $meta->resultados_esperados,
                'indicador_resultados' => $meta->indicador_resultados,
                'fecha_inicio' => $meta->fecha_inicio,
                'fecha_fin' => $meta->fecha_fin,
                'comentario' => $meta->comentario,

                // ✅ Evidencias de la meta (en array seguro)
                'evidencias' => is_array($meta->evidencia)
                    ? $meta->evidencia
                    : json_decode($meta->evidencia, true) ?? ($meta->evidencia ? [$meta->evidencia] : []),

                // ✅ Actividades
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

                        // ✅ Evidencias (en array)
                        'evidencias' => is_array($actividad->evidencia)
                            ? $actividad->evidencia
                            : json_decode($actividad->evidencia, true) ?? ($actividad->evidencia ? [$actividad->evidencia] : []),

                        // ✅ Seguimientos
                        'seguimientos' => $actividad->seguimientos->map(function ($seguimiento) {
                            return [
                                'id' => $seguimiento->id,
                                'periodo_consultar' => $seguimiento->periodo_consultar,
                                'observaciones' => $seguimiento->observaciones,
                                'estado' => $seguimiento->estado,
                                'documento' => $seguimiento->documento,

                                // ✅ Evidencias de seguimiento
                                'evidencias' => is_array($seguimiento->evidencia)
                                    ? $seguimiento->evidencia
                                    : json_decode($seguimiento->evidencia, true) ?? ($seguimiento->evidencia ? [$seguimiento->evidencia] : []),
                            ];
                        }),
                    ];
                }),
            ];
        });

        // ✅ Guardar el backup limpio (sin doble codificación)
        BackupPlan::create([
            'idPlanOriginal' => $plan->id,
            'idDepartamento' => $plan->idDepartamento,
            'idUsuario' => $plan->idUsuario,
            'nombre_plan_estrategico' => $plan->nombre_plan_estrategico,
            'nombre_departamento' => $plan->departamento->departamento ?? null,
            'nombre_responsable' => $plan->responsable->nombre_usuario ?? null,
            'metas' => json_encode($metasBackup, JSON_UNESCAPED_UNICODE),
            'ejes_estrategicos' => json_encode($ejesPlan, JSON_UNESCAPED_UNICODE),
            'objetivos' => $plan->objetivos,
            'fecha_inicio' => $plan->fecha_inicio,
            'fecha_fin' => $plan->fecha_fin,
            'indicador' => $plan->indicador,
            'creado_por' => Auth::id(),
        ]);

        return back()->with('success', 'Backup completo creado correctamente.');
    }

    private function normalizarEjes($valor)
    {
        if (empty($valor)) {
            return [];
        }

        // Si ya es array
        if (is_array($valor)) {
            return array_values(array_filter(array_map(fn($v) => trim($v, "• \t\n\r\""), $valor)));
        }

        // Si viene doblemente codificado en JSON (caso actual)
        $decoded = json_decode($valor, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (is_array($decoded)) {
            // Limpiar texto interno
            return array_values(array_filter(array_map(fn($v) => trim($v, "• \t\n\r\""), $decoded)));
        }

        // Detectar formato enumerado tipo 1. "texto", 2. "texto"
        if (preg_match_all('/\d+\.\s*"?([^"\n]+)"?/u', $valor, $matches)) {
            return array_values(array_map('trim', $matches[1]));
        }

        // Detectar si el texto tiene bullets (•) y tratarlos como un solo eje
        if (preg_match('/•/u', $valor)) {
            // Reemplaza bullets por comas en un mismo texto
            $valor = preg_replace('/\s*•\s*/u', ', ', $valor);
            // Dividir solo si parece haber varios ejes grandes
            if (preg_match('/\b(Gesti[oó]n\s+Urbana)\b/u', $valor)) {
                [$eje1, $eje2] = explode('Gestión Urbana', $valor, 2);
                return [
                    trim($eje1, "•, \t\n\r\""),
                    'Gestión Urbana'
                ];
            }
            return [trim($valor, "•, \t\n\r\"")];
        }

        // Si hay saltos de línea, dividir por líneas
        if (str_contains($valor, "\n")) {
            $lineas = preg_split('/[\r\n]+/', $valor);
            return array_values(array_filter(array_map(fn($l) => trim($l, "• \t\n\r\""), $lineas)));
        }

        // Fallback: devolver como único eje
        return [trim($valor, "• \t\n\r\"")];
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

    public function descargarBackup($id)
    {
        $backup = BackupPlan::findOrFail($id);

        // Decodificar solo metas al momento de render
        $backup->metas = is_string($backup->metas)
            ? json_decode($backup->metas, true) ?? []
            : (is_array($backup->metas) ? $backup->metas : []);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('planes.ver_backup_pdf', compact('backup'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ])
            ->setPaper('a4', 'portrait');

        return $pdf->download('Respaldo_' . \Illuminate\Support\Str::slug($backup->nombre_plan_estrategico) . '.pdf');
    }
}
