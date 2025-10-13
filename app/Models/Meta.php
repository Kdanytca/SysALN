<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        'idPlanEstrategico',
        'idEncargadoMeta',
        'tipo',
        'nombre',
        'objetivos_estrategias',
        'ejes_estrategicos',
        'nombre_actividades',
        'resultados_esperados',
        'indicador_resultados',
        'fecha_inicio',
        'fecha_fin',
        'comentario',
    ];

    protected $casts = [
        'objetivos_estrategias' => 'array',
        'ejes_estrategicos' => 'array',
        'nombre_actividades' => 'array',
    ];

    // Relaciones
    public function planEstrategico()
    {
        return $this->belongsTo(PlanEstrategico::class, 'idPlanEstrategico');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'idMetas');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoMeta', 'id');
    }

    public function encargadoMeta()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoMeta');
    }

}
