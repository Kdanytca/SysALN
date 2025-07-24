<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        Usuario::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'nombre_usuario' => 'admin',
                'password' => Hash::make('admin1234'),
                'tipo_usuario' => 'administrador',
                'idDepartamento' => null,
                'idInstitucion' => null,
            ]
        );
    }
}

