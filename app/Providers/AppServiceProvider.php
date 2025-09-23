<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\HistorialSesion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Evento de Login
        Event::listen(Login::class, function ($event) {
            HistorialSesion::create([
                'idUsuario'    => $event->user->id,
                'nombre_usuario' => $event->user->nombre_usuario,
                'login_at'   => now(),
            ]);
        });

        // Evento de Logout
        Event::listen(Logout::class, function ($event) {
            $session = HistorialSesion::where('idUsuario', $event->user->id)
                        ->whereNull('logout_at')
                        ->latest()
                        ->first();

            if ($session) {
                $session->update([
                    'logout_at' => now(),
                ]);
            }
        });
    }
}
