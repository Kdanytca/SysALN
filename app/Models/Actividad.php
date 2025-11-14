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
        'indicadores',
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

    // ⚙️ Boot para eliminación en cascada
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($actividad) {
            // 1️⃣ Borrar seguimientos antes de eliminar la actividad
            foreach ($actividad->seguimientos as $seguimiento) {
                $seguimiento->delete();
            }

            // 2️⃣ (Opcional) Eliminar archivos de evidencia de la actividad
            if (!empty($actividad->evidencia)) {
                $rutas = json_decode($actividad->evidencia, true);
                if (is_array($rutas)) {
                    foreach ($rutas as $ruta) {
                        $archivo = public_path($ruta);
                        if (file_exists($archivo)) {
                            @unlink($archivo);
                        }
                    }
                }
            }
        });
    }
}
