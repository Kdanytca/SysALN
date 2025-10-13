<?php

namespace App\Http\Controllers;

use App\Models\Resultado;
use App\Models\PlanEstrategico;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;



class ResultadoController extends Controller
{
    public function index($idPlan)
    {
        $plan = PlanEstrategico::findOrFail($idPlan);
        $resultados = Resultado::where('idPlanEstrategico', $idPlan)->orderBy('fecha_consulta')->get();

        $fechas = $resultados->map(function ($r) {
            return Carbon::createFromFormat('Ymd', $r->fecha_consulta)->format('d-m-Y');
        });

        $porcentajes = $resultados->pluck('porcentaje_seguimiento');

        return view('planes.resultados', compact('plan', 'resultados', 'fechas', 'porcentajes'));
    }

    public function verReporte($id)
    {
        $plan = PlanEstrategico::with('metas.actividades.seguimientos')->findOrFail($id);

        $data = [];
        $descripcionGeneral = '';
        $comentarios = [];
        $totalSeguimientosGlobal = 0;
        $seguimientosFinalizadosGlobal = 0;

        foreach ($plan->metas as $meta) {
            $actividades = $meta->actividades ?? collect();

            if ($actividades->isEmpty()) {
                continue;
            }

            // Contadores para la meta
            $totalSeguimientos = 0;
            $seguimientosFinalizados = 0;

            foreach ($actividades as $actividad) {
                $seguimientos = $actividad->seguimientos;
                $totalSeguimientos += $seguimientos->count();
                $seguimientosFinalizados += $seguimientos->filter(function ($s) {
                    return strtolower(trim($s->estado ?? '')) === 'finalizado';
                })->count();
            }

            // Calcula el porcentaje de avance en base a seguimientos finalizados
            $porcentajeMeta = $totalSeguimientos > 0
                ? round(($seguimientosFinalizados / $totalSeguimientos) * 100)
                : 0;

            // Acumula para el cálculo global
            $totalSeguimientosGlobal += $totalSeguimientos;
            $seguimientosFinalizadosGlobal += $seguimientosFinalizados;

            $data[] = [
                'meta' => $meta->nombre,
                'descripcion' => "$totalSeguimientos seguimientos - $seguimientosFinalizados finalizados",
                'porcentaje' => $porcentajeMeta,
                'comentario' => $meta->comentario ?? '',
                'indicador' => match (true) {
                    $porcentajeMeta >= 75 => 'verde',
                    $porcentajeMeta >= 40 => 'amarillo',
                    default => 'rojo',
                },
            ];

            $descripcionGeneral .= $meta->nombre . ': ' . $meta->descripcion . '. ';
            $comentarios[] = $meta->comentario ?? '';
        }

        // Porcentaje global basado en todos los seguimientos del plan
        $porcentajeTotal = $totalSeguimientosGlobal > 0
            ? round(($seguimientosFinalizadosGlobal / $totalSeguimientosGlobal) * 100)
            : 0;

        return view('planes.resultados', [
            'plan' => $plan,
            'data' => $data,
            'fechas' => [],
            'porcentajes' => [],
            'porcentajeTotal' => $porcentajeTotal,
        ]);
    }
    //Metodo para generar el reporte en PDF
    public function generarPDF($id)
    {
        $plan = PlanEstrategico::with([
            'departamento.institucion',
            'responsable', // del plan
            'metas.responsable', // del meta
            'metas.actividades.usuario', // del actividad
            'metas.actividades.seguimientos',
        ])->findOrFail($id);

        //dd($plan->metas->pluck('responsable'));

        $data = [];
        $totalSeguimientosGlobal = 0;
        $seguimientosFinalizadosGlobal = 0;

        foreach ($plan->metas as $meta) {
            $actividades = $meta->actividades ?? collect();
            if ($actividades->isEmpty()) continue;

            $totalSeguimientos = 0;
            $seguimientosFinalizados = 0;

            foreach ($actividades as $actividad) {
                $seguimientos = $actividad->seguimientos;
                $totalSeguimientos += $seguimientos->count();
                $seguimientosFinalizados += $seguimientos->filter(
                    fn($s) =>
                    strtolower(trim($s->estado ?? '')) === 'finalizado'
                )->count();
            }

            $porcentajeMeta = $totalSeguimientos > 0
                ? round(($seguimientosFinalizados / $totalSeguimientos) * 100)
                : 0;

            $totalSeguimientosGlobal += $totalSeguimientos;
            $seguimientosFinalizadosGlobal += $seguimientosFinalizados;

            $data[] = [
                'meta' => $meta->nombre,
                'descripcion' => "$totalSeguimientos seguimientos - $seguimientosFinalizados finalizados",
                'porcentaje' => $porcentajeMeta,
                'comentario' => $meta->comentario ?? '',
                'indicador' => match (true) {
                    $porcentajeMeta >= 75 => 'verde',
                    $porcentajeMeta >= 40 => 'amarillo',
                    default => 'rojo',
                },
            ];
        }

        $porcentajeTotal = $totalSeguimientosGlobal > 0
            ? round(($seguimientosFinalizadosGlobal / $totalSeguimientosGlobal) * 100)
            : 0;

        $pdf = PDF::loadView('planes.reporte-pdf', compact('plan', 'data', 'porcentajeTotal'));
        return $pdf->download('reporte_plan_' . $plan->id . '.pdf');
    }
}
