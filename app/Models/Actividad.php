<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    public function meta()
    {
        return $this->belongsTo(Metas::class, 'idMetas');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoActividades::class, 'idActividades');
    }

}
