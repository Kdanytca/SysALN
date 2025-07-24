<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = [
        'nombre_institucion',
        'tipo_institucion',
        'idEncargadoInstitucion',
    ];

    // Relaciones
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idInstitucion');
    }
    
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idInstitucion');
    }

    public function encargadoInstitucion()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoInstitucion');
    }

}
