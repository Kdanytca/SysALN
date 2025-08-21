<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $plan = $user->planEstrategico;


        // Redirigir según tipo de usuario y su ID asignado
        switch ($user->tipo_usuario) {
            case 'administrador':
                return redirect()->intended('/dashboard');

            case 'encargado_institucion':
                return redirect()->intended("/dashboard");

            case 'encargado_departamento':
                return redirect()->intended("/departamento/{$user->idDepartamento}");

            case 'responsable_plan':
                $plan = $user->planEstrategico; // usa el nombre correcto
                if ($plan) {
                    return redirect()->intended("/plan-estrategico/{$plan->id}");
                } else {
                    return redirect()->intended('/sin-plan-asignado');
                }
            case 'responsable_meta':
                return redirect()->intended(route('meta.responsable'));
            case 'responsable_actividad':
                return redirect()->intended(route('actividades.indexResponsable'));

            default:
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Rol no autorizado.',
                ]);
        }
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
