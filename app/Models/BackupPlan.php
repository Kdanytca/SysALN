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
        'nombre_departamento', // opcional: guardado directamente en backup
        'nombre_responsable',  // opcional: guardado directamente en backup
    ];

    // Relación opcional al plan original
    public function planOriginal()
    {
        return $this->belongsTo(PlanEstrategico::class, 'idPlanOriginal')->withDefault();
    }
}
