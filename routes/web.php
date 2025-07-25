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
use App\Http\Controllers\SeguimientoActividadController;
use App\Http\Controllers\ResultadoController;
use App\Http\Middleware\TipoUsuario;

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


Route::middleware(['auth', 'verified'])->group(function () {
    // Institucion
    Route::resource('instituciones', InstitucionController::class)->only([
        'index',
        'store',
        'update',
        'destroy'
    ]);
    Route::get('instituciones/create', fn() => abort(404));
    Route::get('instituciones/edit', fn() => abort(404));

    // Departamento
    Route::resource('departamentos', DepartamentoController::class);
    
    // usuarios
    Route::get('/usuarios/{id}', [UsuarioController::class, 'showJson']);
    Route::resource('usuarios', UsuarioController::class);

    // Metas
    Route::resource('metas', MetaController::class);
    Route::get('/planes/{plan}/metas', [MetaController::class, 'indexPorPlan'])->name('plan.metas');
    
    // Actividades
    Route::resource('actividades', ActividadController::class);
    Route::get('/metas/{meta}/actividades', [ActividadController::class, 'indexPorMeta'])->name('meta.actividades');
    //seguimiento de actividades
    Route::get('/actividades/{actividad}/seguimientos', [\App\Http\Controllers\SeguimientoActividadController::class, 'listarPorActividad']);
    Route::delete('/seguimientos/{seguimiento}', [\App\Http\Controllers\SeguimientoActividadController::class, 'destroy'])->name('seguimientos.destroy');
    //resumen de actividades por meta
    Route::get('/metas/{meta}/resumen-seguimientos', [SeguimientoActividadController::class, 'resumenPorMeta'])->name('meta.resumen_seguimientos');




    // Planes Estratégicos
    Route::post('/planes', [PlanEstrategicoController::class, 'store'])->name('planes.store');
    Route::get('/planes', [PlanEstrategicoController::class, 'index'])->name('planes.index');
    //eliminar un plan estratégico
    Route::delete('/planes/{id}', [PlanEstrategicoController::class, 'destroy'])->name('planes.destroy');
    //Actualizar un plan estratégico
    Route::put('/planes/{id}', [PlanEstrategicoController::class, 'update'])->name('planes.update');
    //obtener usuarios por departamento
    Route::get('/departamentos/{id}/usuarios-disponibles', [App\Http\Controllers\UsuarioController::class, 'usuariosPorDepartamento']);
    //mostrar planes por institución
    Route::get('/instituciones/{id}/planes', [App\Http\Controllers\PlanEstrategicoController::class, 'planesPorInstitucion'])->name('institucion.planes');
    //mostrar planes globales
    Route::get('/planes/todos', [PlanEstrategicoController::class, 'indexGlobal'])->name('planes.global');

    // Mostrar resultados registrados
    Route::get('/planes/{id}/reporte', [ResultadoController::class, 'verReporte'])->name('planes.reporte');





    // Seguimiento de Actividades
    Route::get('/seguimientos', [SeguimientoActividadController::class, 'index'])->name('seguimientos.index');
    Route::get('/seguimientos/create', [SeguimientoActividadController::class, 'create'])->name('seguimientos.create');
    Route::post('/seguimientos', [SeguimientoActividadController::class, 'store'])->name('seguimientos.store');
    Route::get('/seguimientos/{seguimiento}', [SeguimientoActividadController::class, 'show']);
    Route::put('/seguimientos/{id}', [SeguimientoActividadController::class, 'update'])->name('seguimientos.update');

});

require __DIR__ . '/auth.php';
