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
}
