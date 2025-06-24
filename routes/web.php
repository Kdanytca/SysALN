<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\PlanEstrategicoController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Aquí cambias la ruta para usar el controlador y no una closure
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified'])->group(function(){
    // Institucion
    Route::resource('instituciones', InstitucionController::class);
    
    // Departamento
    Route::resource('departamentos', DepartamentoController::class);
    Route::get('/instituciones/{institucion}/departamentos', [DepartamentoController::class, 'indexPorInstitucion'])
        ->name('institucion.departamentos');
    // Para mostrar todos los departamentos
    Route::get('/departamentos', [DepartamentoController::class, 'todos'])->name('departamentos.index');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
    
    // Metas
    Route::resource('metas', MetaController::class);
    
    // Actividades
    Route::resource('actividades', ActividadController::class);

    // Planes Estratégicos
    Route::get('/instituciones/{id}/planes/create', [PlanEstrategicoController::class, 'create'])->name('planes.create');
    Route::post('/planes', [PlanEstrategicoController::class, 'store'])->name('planes.store');
    Route::get('/planes', [PlanEstrategicoController::class, 'index'])->name('planes.index');
    //eliminar un plan estratégico
    Route::delete('/planes/{id}', [PlanEstrategicoController::class, 'destroy'])->name('planes.destroy');
    //Actualizar un plan estratégico
    Route::put('/planes/{id}', [PlanEstrategicoController::class, 'update'])->name('planes.update');
});

require __DIR__ . '/auth.php';
