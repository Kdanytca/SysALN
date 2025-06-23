<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    public function actividades()
    {
        return $this->hasMany(Actividades::class, 'idMetas');
    }

}
