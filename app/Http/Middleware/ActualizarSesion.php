<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\HistorialSesion;

class ActualizarSesion
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $sesion = HistorialSesion::where('idUsuario', auth()->id())
                        ->whereNull('logout_at')
                        ->latest()
                        ->first();

            if ($sesion) {
                // Verificar si la última actividad fue hace más de 30 minutos
                if ($sesion->last_activity && $sesion->last_activity < now()->subMinutes(30)) {
                    $sesion->update([
                        'logout_at' => now(),
                    ]);

                    // Forzar logout
                    auth()->logout();

                    return redirect()->route('login')->with('status', 'Tu sesión expiró por inactividad.');
                }

                // Actualizar la última actividad
                $sesion->update(['last_activity' => now()]);
            }
        }

        return $next($request);
    }
}
