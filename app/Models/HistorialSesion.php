<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialSesion extends Model
{
    protected $table = 'historial_sesiones';

    protected $fillable = [
        'idUsuario',
        'nombre_usuario',
        'login_at',
        'logout_at',
        'last_activity',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }
}
