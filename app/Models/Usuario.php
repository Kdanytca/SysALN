<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public function instituciones()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function departamentos()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
