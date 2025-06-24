<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idInstitucion');
    }
}
