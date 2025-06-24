<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

//Demo planes estrategicos
Route::get('/plan', function () {
    $planes = [
        [
            'nombre_plan_estrategico' => 'Plan de Innovación 2025',
            'metas' => 'Reducir costos en 20%',
            'ejes_estrategicos' => 'Tecnología, Capacitación',
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2025-12-31',
            'indicador' => 'Porcentaje de reducción',
            'responsable' => 'Carlos Méndez',
        ],
        [
            'nombre_plan_estrategico' => 'Plan de Expansión Regional',
            'metas' => 'Abrir 3 nuevas sedes',
            'ejes_estrategicos' => 'Infraestructura, Recursos Humanos',
            'fecha_inicio' => '2025-06-01',
            'fecha_fin' => '2026-06-01',
            'indicador' => 'Número de sedes abiertas',
            'responsable' => 'María López',
        ],
    ];

    return view('demo.plan', compact('planes'));
});

require __DIR__ . '/auth.php';
