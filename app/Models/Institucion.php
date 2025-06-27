<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = [
        'nombre_institucion',
        'tipo_institucion',
        'encargado_proyecto',
    ];

    // Relaciones
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idInstitucion');
    }
}
