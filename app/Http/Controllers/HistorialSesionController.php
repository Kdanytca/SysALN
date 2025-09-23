<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialSesion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistorialSesionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = HistorialSesion::with('usuario');

        // Filtro según tipo de usuario
        if ($user->tipo_usuario === 'encargado_institucion') {
            $query->whereHas('usuario', function ($q) use ($user) {
                $q->where('idInstitucion', $user->idInstitucion);
            });
        } elseif ($user->tipo_usuario !== 'administrador') {
            // Otros usuarios solo ven su propio historial
            $query->where('idUsuario', $user->id);
        }

        // Validar campos de filtro
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_cierre' => 'nullable|date|after_or_equal:fecha_inicio',
            // Opcional: validar hora también si quieres
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
        ], [
            'fecha_cierre.after_or_equal' => 'La fecha de cierre debe ser igual o posterior a la fecha de inicio.',
        ]);

        // Validación cruzada fecha + hora combinadas
        if (
            $request->filled(['fecha_inicio', 'hora_inicio', 'fecha_cierre', 'hora_cierre'])
        ) {
            $inicio = Carbon::createFromFormat('Y-m-d H:i', $request->fecha_inicio . ' ' . $request->hora_inicio);
            $cierre = Carbon::createFromFormat('Y-m-d H:i', $request->fecha_cierre . ' ' . $request->hora_cierre);

            if ($inicio->gt($cierre)) {
                return back()
                    ->withErrors(['hora_inicio' => 'La fecha y hora de inicio no pueden ser posteriores a la fecha y hora de cierre.'])
                    ->withInput();
            }
        }

        // Filtros por búsqueda y fechas
        if ($request->filled('buscar')) {
            $query->where('nombre_usuario', 'like', '%' . $request->buscar . '%');
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('login_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_cierre')) {
            $query->whereDate('logout_at', '<=', $request->fecha_cierre);
        }

        if ($request->filled('hora_inicio') && $request->filled('fecha_inicio')) {
            $query->whereTime('login_at', '>=', $request->hora_inicio);
        }

        if ($request->filled('hora_cierre') && $request->filled('fecha_cierre')) {
            $query->whereTime('logout_at', '<=', $request->hora_cierre);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'activo') {
                // Sin logout y last_activity no ha expirado
                $query->whereNull('logout_at')
                    ->where(function ($q) {
                        $q->whereNull('last_activity')
                            ->orWhere('last_activity', '>=', now()->subMinutes(15));
                    });

            } elseif ($request->estado === 'cerrado') {
                $query->whereNotNull('logout_at');

            } elseif ($request->estado === 'expirado') {
                $query->whereNull('logout_at')
                    ->where('last_activity', '<', now()->subMinutes(15));
            }
        }

        $historial = $query->orderBy('login_at', 'desc')->paginate(15);

        return view('historial_sesion.index', compact('historial'));
    }
}
