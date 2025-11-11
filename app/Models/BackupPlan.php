<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupPlan extends Model
{
    protected $table = 'backup_planes';

    protected $fillable = [
        'idPlanOriginal',
        'idDepartamento',
        'idUsuario',
        'nombre_plan_estrategico',
        'metas',
        'ejes_estrategicos',
        'objetivos',
        'fecha_inicio',
        'fecha_fin',
        'indicador',
        'creado_por',
        'nombre_departamento',
        'nombre_responsable',
    ];

    /**
     * Casting de campos JSON a arrays.
     */
    protected $casts = [
        'metas'             => 'array',
        'ejes_estrategicos' => 'array',
        'objetivos'         => 'array',
        'fecha_inicio'      => 'date',
        'fecha_fin'         => 'date',
    ];

    public function planOriginal()
    {
        return $this->belongsTo(PlanEstrategico::class, 'idPlanOriginal')->withDefault();
    }
}
