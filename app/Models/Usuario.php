<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function planesEstrategicos()
    {
        return $this->hasMany(PlanEstrategico::class, 'idUsuario');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'idUsuario');
    }

}
