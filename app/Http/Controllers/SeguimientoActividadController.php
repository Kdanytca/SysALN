<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\SeguimientoActividad;
use Illuminate\Http\Request;
use App\Models\Meta;

class SeguimientoActividadController extends Controller
{
    public function index()
    {
        $seguimientos = SeguimientoActividad::with('actividad')->latest()->get();
        return view('seguimientos.index', compact('seguimientos'));
    }

    public function create()
    {
        $actividades = Actividad::all();
        return view('seguimientos.create', compact('actividades'));
    }

    // Crear seguimiento
    public function store(Request $request)
    {
        $request->validate([
            'periodo_consultar' => 'required|date',
            'idActividades' => 'required|exists:actividades,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,en progreso,finalizado',
        ]);

        $seguimiento = SeguimientoActividad::create($request->only('periodo_consultar', 'idActividades', 'observaciones', 'estado'));

        $actividad = Actividad::findOrFail($request->idActividades);
        $idMeta = $actividad->idMetas; // Asegúrate que exista esta relación o atributo

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('meta.actividades', $idMeta)
            ->with('success', 'Seguimiento registrado correctamente.');
    }

    // Lista los seguimientos de una actividad específica
    public function listarPorActividad(Actividad $actividad)
    {
        $seguimientos = $actividad->seguimientos()->latest()->get();
        return view('seguimientos._tabla', compact('seguimientos'));
    }

    // Elimina un seguimiento de actividad
    public function destroy(SeguimientoActividad $seguimiento)
    {
        $seguimiento->delete();
        return response()->json(['success' => true]);
    }

    // Resumen de seguimientos por meta
    public function resumenPorMeta(Meta $meta)
    {
        $actividades = $meta->actividades()->with('seguimientos')->get();
        return view('seguimientos.resumen', compact('meta', 'actividades'));
    }
    // Edita un seguimiento de actividad
    public function update(Request $request, $id)
    {
        $request->validate([
            'periodo_consultar' => 'required|date',
            'idActividades' => 'required|exists:actividades,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,en progreso,finalizado',
        ]);

        $seguimiento = SeguimientoActividad::findOrFail($id);

        $seguimiento->periodo_consultar = $request->input('periodo_consultar');
        $seguimiento->estado = $request->input('estado');
        $seguimiento->observaciones = $request->input('observaciones');
        $seguimiento->idActividades = $request->input('idActividades');
        $seguimiento->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Seguimiento actualizado correctamente.');
    }
}
