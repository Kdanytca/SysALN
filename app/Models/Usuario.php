<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $fillable = [
        'idDepartamento',
        'nombre_usuario',
        'password',
        'tipo_usuario',
        'email',
    ];

    // Relaciones
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'idUsuario');
    }

    public function planesEstrategicos()
    {
        return $this->hasMany(PlanEstrategico::class, 'idUsuario');
    }
}

