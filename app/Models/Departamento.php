<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public function instituciones()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

}
