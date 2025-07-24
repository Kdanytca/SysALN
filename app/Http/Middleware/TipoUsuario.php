<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TipoUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$tipos)
    {
        if (Auth::check() && in_array(Auth::user()->tipo_usuario, $tipos)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
