<?php
namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\PlanEstrategico;
use App\Models\Departamento;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        $institucionesCount = Institucion::count();
        $planesCount = PlanEstrategico::count();
        $departamentosCount = Departamento::count();
        $usuariosCount = Usuario::count();
        

        return view('bienvenido', compact('institucionesCount', 'planesCount', 
        'departamentosCount', 'usuariosCount'));
    }
}
