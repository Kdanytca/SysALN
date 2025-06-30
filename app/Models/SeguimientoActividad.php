<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoActividad extends Model
{
    protected $table = 'seguimiento_actividades';

    protected $fillable = [
        'periodo_consultar',
        'observaciones',
        'estado',
        'documento',
        'idActividades',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'idActividades');
    }
    
}
