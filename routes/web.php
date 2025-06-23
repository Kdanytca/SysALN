<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('bienvenido');
    })->name('dashboard');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

//Institucion
Route::resource('instituciones', InstitucionController::class);

//Departamento
Route::resource('departamentos', DepartamentoController::class);
Route::get('/instituciones/{institucion}/departamentos', [DepartamentoController::class, 'indexPorInstitucion'])
    ->name('institucion.departamentos');

//Usuarios
Route::resource('usuarios', UsuarioController::class)->middleware('auth');
