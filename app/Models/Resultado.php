<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $fillable = [
        'idPlanEstrategico',
        'fecha_consulta',
        'descripcion',
        'porcentaje_seguimiento',
        'indicador',
        'comentarios',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanEstrategico::class, 'idPlanEstrategico');
    }
}
