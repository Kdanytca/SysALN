<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = [
        'departamento',
        'idEncargadoDepartamento',
        'idInstitucion',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($departamento) {
            foreach ($departamento->planes as $plan) {
                $plan->delete();
            }
        });
    }

    // Relaciones
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'idInstitucion');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idDepartamento');
    }

    public function encargadoDepartamento()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoDepartamento');
    }
    public function planes()
    {
        return $this->hasMany(PlanEstrategico::class, 'idDepartamento');
    }
}
