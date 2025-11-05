<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoActividad extends Model
{
    protected $table = 'seguimiento_actividades';

    protected $fillable = [
        'periodo_consultar',
        'observaciones',
        'estado',
        'documento',
        'idActividades',
        'evidencia',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'idActividades');
    }
    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($seguimiento) {
            if (!empty($seguimiento->evidencia)) {
                $rutas = json_decode($seguimiento->evidencia, true);

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
