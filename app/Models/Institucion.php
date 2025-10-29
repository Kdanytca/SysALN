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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($institucion) {
            // Borrar los departamentos
            foreach ($institucion->departamentos as $departamento) {
                // Borrar los planes de cada departamento
                foreach ($departamento->planes as $plan) {
                    $plan->delete(); // Esto disparará el deleting() de Plan y luego los demás
                }
            }
        });
    }

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
    public function planes()
    {
        return $this->hasManyThrough(
            PlanEstrategico::class,   // Modelo destino
            Departamento::class,      // Modelo intermedio
            'idInstitucion',          // FK en departamentos
            'idDepartamento',         // FK en planes
            'id',                     // PK en instituciones
            'id'                      // PK en departamentos
        );
    }
}
