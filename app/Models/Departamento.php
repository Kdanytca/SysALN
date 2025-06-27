<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = [
        'departamento',
        'encargado_departamento',
        'idInstitucion',
    ];

    // Relaciones
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'idInstitucion');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idDepartamento');
    }

}
