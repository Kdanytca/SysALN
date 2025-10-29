<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PlanEstrategico extends Model
{
    protected $table = 'planes_estrategicos';

    protected $fillable = [
        'idDepartamento',
        'idUsuario',
        'nombre_plan_estrategico',
        'ejes_estrategicos',
        'fecha_inicio',
        'fecha_fin',
        'indicador',
        'responsable',
        'creado_por',
        'objetivos',
    ];

    // Relaciones

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function metas()
    {
        return $this->hasMany(Meta::class, 'idPlanEstrategico');
    }

    //Indicador de progreso
    public function getIndicadorAttribute()
    {
        // Si está finalizado, devuelve el valor guardado
        if (!is_null($this->attributes['indicador']) && $this->attributes['indicador'] === 'finalizado') {
            return 'finalizado';
        }

        $hoy = Carbon::today();
        $inicio = Carbon::parse($this->fecha_inicio);
        $fin = Carbon::parse($this->fecha_fin);
        $totalDias = $inicio->diffInDays($fin);
        $transcurridos = $inicio->diffInDays($hoy);

        if ($hoy->greaterThan($fin)) {
            return 'rojo';
        } elseif ($transcurridos >= $totalDias) {
            return 'verde';
        } elseif ($transcurridos >= $totalDias / 2) {
            return 'amarillo';
        } else {
            return 'blanco';
        }
    }
    public function backup()
    {
        return $this->hasOne(BackupPlan::class, 'idPlanOriginal');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($plan) {
            // Borrar las metas asociadas antes de eliminar el plan
            foreach ($plan->metas as $meta) {
                $meta->delete(); // Esto activará el deleting() de Meta y luego el de Actividad
            }
        });
    }
}
