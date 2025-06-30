<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        'idPlanEstrategico',
        'usuario_responsable',
        'nombre_meta',
        'ejes_estrategicos',
        'nombre_actividades',
        'fecha_inicio',
        'fecha_fin',
        'comentario',
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
}
