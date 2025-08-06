<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'idDepartamento',
        'idInstitucion',
        'nombre_usuario',
        'password',
        'tipo_usuario',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getTipoUsuarioLabelAttribute()
    {
        return match ($this->tipo_usuario) {
            'administrador' => 'Administrador',
            'encargado_institucion' => 'Encargado de Institución',
            'encargado_departamento' => 'Encargado de Departamento',
            'responsable_plan' => 'Responsable de Plan Estratégico',
            'responsable_meta' => 'Responsable de Meta',
            'responsable_actividad' => 'Responsable de Actividad',
            default => ucfirst($this->tipo_usuario),
        };
    }

    // Relaciones
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'idInstitucion');
    }

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'idEncargadoInstitucion');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idEncargadoDepartamento');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'idUsuario');
    }

    public function planEstrategico()
    {
        return $this->hasOne(PlanEstrategico::class, 'idUsuario');
    }
}
