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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HistorialSesionController;
use Illuminate\Support\Facades\Http;



// Ruta principal
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard y perfil (todos los autenticados)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para el chat con contexto
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});

// =========================
// ADMINISTRADOR (solo instituciones)
// =========================
Route::middleware(['auth', 'verified', TipoUsuario::class . ':administrador'])->group(function () {
    Route::resource('instituciones', InstitucionController::class)->only([
        'index',
        'store',
        'update',
        'destroy'
    ]);

    Route::get('instituciones/create', fn() => abort(404));
    Route::get('instituciones/edit', fn() => abort(404));

    // Eliminar una institución con todos sus datos relacionados (departamentos, planes, usuarios, etc.)
    Route::delete('/instituciones/{id}/eliminarConUsuarios', [InstitucionController::class, 'eliminarInstitucionConUsuarios'])->name('instituciones.eliminarInstitucionConUsuarios');
});

// =========================
// HISTORIAL DE SESIONES (solo admins o encargados)
// =========================
Route::middleware(['auth', 'verified', TipoUsuario::class . ':administrador,encargado_institucion'])->group(function () {
    // Historial de sesiones
    Route::get('/historial-sesiones', [HistorialSesionController::class, 'index'])->name('historial_sesion.index');
});

// =========================
// USUARIOS
// =========================

// Solo administrador puede ver usuarios
Route::middleware(['auth', 'verified', TipoUsuario::class . ':administrador'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'showJson'])->name('usuarios.show');
});

// Todos los roles autenticados pueden crear, editar y eliminar usuarios
Route::middleware(['auth', 'verified', TipoUsuario::class . ':administrador,encargado_institucion,encargado_departamento,responsable_plan,responsable_meta,responsable_actividad'])->group(function () {
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::post('/usuarios/verificar-unico', [UsuarioController::class, 'verificarUnico'])->name('usuarios.verificarUnico');
});

// =========================
// OTROS ROLES (excluye administrador en instituciones, pero puede usar usuarios)
// =========================
Route::middleware(['auth', 'verified', TipoUsuario::class . ':encargado_institucion,encargado_departamento,responsable_plan,responsable_meta,responsable_actividad'])->group(function () {
    // Departamentos
    Route::resource('departamentos', DepartamentoController::class);
    Route::get('/instituciones/{institucion}/departamentos', [DepartamentoController::class, 'indexPorInstitucion'])->name('institucion.departamentos');
    Route::get('/departamentos', [DepartamentoController::class, 'todos'])->name('departamentos.index_general');

    // Metas
    Route::resource('metas', MetaController::class);
    Route::get('/planes/{plan}/metas', [MetaController::class, 'indexPorPlan'])->name('plan.metas');
    Route::get('/planes/{plan}/metas/create', [MetaController::class, 'createDesdePlan'])->name('metas.createDesdePlan');
    Route::get('/mis-metas', [MetaController::class, 'indexResponsable'])->name('meta.responsable');

    // Actividades
    Route::resource('actividades', ActividadController::class)->except(['show']);
    Route::get('/metas/{meta}/actividades', [ActividadController::class, 'indexPorMeta'])->name('meta.actividades');
    Route::get('/actividades/responsable', [ActividadController::class, 'indexResponsable'])->name('actividades.indexResponsable');
    Route::post('/metas/{meta}/actividades/{actividad}/evidencias', [ActividadController::class, 'guardarEvidencias'])->name('actividades.guardarEvidencias');

    //Rango de fechas para seguimientos
    Route::get('/actividades/{id}/rango-fechas', [ActividadController::class, 'rangoFechas']);


    // Seguimiento de Actividades
    Route::get('/actividades/{actividad}/seguimientos', [SeguimientoActividadController::class, 'listarPorActividad']);
    Route::delete('/seguimientos/{seguimiento}', [SeguimientoActividadController::class, 'destroy'])->name('seguimientos.destroy');
    Route::get('/metas/{meta}/resumen-seguimientos', [SeguimientoActividadController::class, 'resumenPorMeta'])->name('meta.resumen_seguimientos');
    Route::get('/seguimientos', [SeguimientoActividadController::class, 'index'])->name('seguimientos.index');
    Route::get('/seguimientos/create', [SeguimientoActividadController::class, 'create'])->name('seguimientos.create');
    Route::post('/seguimientos', [SeguimientoActividadController::class, 'store'])->name('seguimientos.store');
    Route::get('/seguimientos/{seguimiento}', [SeguimientoActividadController::class, 'show']);
    Route::put('/seguimientos/{id}', [SeguimientoActividadController::class, 'update'])->name('seguimientos.update');
    Route::post('/seguimientos/{id}/guardar-evidencias', [SeguimientoActividadController::class, 'guardarEvidencias'])->name('seguimientos.guardarEvidencias');

    // Planes Estratégicos
    Route::post('/planes', [PlanEstrategicoController::class, 'store'])->name('planes.store');
    Route::get('/planes', [PlanEstrategicoController::class, 'index'])->name('planes.index');
    Route::delete('/planes/{id}', [PlanEstrategicoController::class, 'destroy'])->name('planes.destroy');
    Route::delete('/planes/{id}/eliminarConUsuarios', [PlanEstrategicoController::class, 'eliminarConUsuarios'])->name('planes.eliminarConUsuarios');
    Route::put('/planes/{id}', [PlanEstrategicoController::class, 'update'])->name('planes.update');
    Route::get('/departamentos/{id}/usuarios-disponibles', [UsuarioController::class, 'usuariosPorDepartamento']);
    Route::get('/instituciones/{id}/planes', [PlanEstrategicoController::class, 'planesPorInstitucion'])->name('institucion.planes');
    Route::get('/planes/todos', [PlanEstrategicoController::class, 'indexGlobal'])->name('planes.global');
    Route::post('/planes/{id}/finalizar', [PlanEstrategicoController::class, 'toggleFinalizar'])->name('planes.finalizar');
    Route::get('/planes/{id}/reporte', [ResultadoController::class, 'verReporte'])->name('planes.reporte');
    Route::get('/plan/{id}/reporte-pdf', [ResultadoController::class, 'generarPDF'])->name('plan.reporte.pdf');
    //Backup de planes
    Route::post('/planes/{id}/backup', [PlanEstrategicoController::class, 'backup'])->name('planes.backup');
    Route::get('/planes/backup/{id}', [PlanEstrategicoController::class, 'verBackup'])->name('planes.verBackup');
    //respaldos
    Route::get('/respaldo-planes', [PlanEstrategicoController::class, 'respaldoIndex'])->name('planes.backupIndex');
    Route::get('/respaldo-planes/{id}', [PlanEstrategicoController::class, 'verBackup'])->name('planes.verBackup');
    //Descargar backup pdf
    Route::get('/planes/backup/{id}/descargar', [PlanEstrategicoController::class, 'descargarBackup'])
        ->name('planes.descargarBackup');



    // Rutas por roles
    Route::get('/institucion/{id}', [InstitucionController::class, 'ver'])->name('institucion.ver');
    Route::get('/plan-estrategico/{id}', [PlanEstrategicoController::class, 'verResponsable'])->name('plan.responsable');
    Route::middleware(['auth', 'verified', TipoUsuario::class . ':encargado_departamento'])->group(function () {
        Route::get('/mi-departamento', [DepartamentoController::class, 'index'])->name('departamento.ver');
    });
});

require __DIR__ . '/auth.php';
