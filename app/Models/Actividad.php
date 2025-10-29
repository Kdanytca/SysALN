<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'idMetas',
        'idEncargadoActividad',
        'nombre_actividad',
        'objetivos',
        'fecha_inicio',
        'fecha_fin',
        'evidencia',
        'comentario',
        'unidad_encargada',
    ];

    protected $casts = [
        'objetivos' => 'array',
        'evidencia' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($actividad) {
            // Si hay evidencias guardadas en formato JSON
            if ($actividad->evidencia) {
                $evidencias = json_decode($actividad->evidencia, true);

                if (is_array($evidencias)) {
                    foreach ($evidencias as $ruta) {
                        $rutaCompleta = public_path($ruta);

                        if (File::exists($rutaCompleta)) {
                            File::delete($rutaCompleta);
                        }
                    }
                }
            }
        });
    }

    // Relaciones
    public function meta()
    {
        return $this->belongsTo(Meta::class, 'idMetas');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoActividad');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoActividad::class, 'idActividades');
    }

    public function encargadoActividad()
    {
        return $this->belongsTo(Usuario::class, 'idEncargadoActividad');
    }
}
