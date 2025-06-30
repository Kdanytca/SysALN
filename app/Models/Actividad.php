<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'idMetas',
        'idUsuario',
        'nombre_actividad',
        'objetivos',
        'fecha_inicio',
        'fecha_fin',
        'resultados_esperados',
        'unidad_encargada',
    ];

    // Relaciones
    public function meta()
    {
        return $this->belongsTo(Meta::class, 'idMetas');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoActividad::class, 'idActividades');
    }
}
