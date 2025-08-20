<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\Usuario;
use App\Models\Departamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    // Vista general (solo admins o encargados)
    public function index()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $actividades = Actividad::with('meta', 'usuario')->get();
        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();

        return view('actividades.index', compact('actividades', 'metas', 'usuarios', 'departamentos'));
    }

    // Vista de actividades filtradas por meta (solo admins o encargados)
    public function indexPorMeta(Meta $meta)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $meta->load('planEstrategico');
        $actividades = $meta->actividades()->with('meta')->get();
        $metas = Meta::all();

        $usuarios = Usuario::where('idInstitucion', $meta->planEstrategico->departamento->idInstitucion)
            ->whereIn('tipo_usuario', ['responsable_actividad', 'encargado_institucion'])
            ->get();

        $departamentos = Departamento::all();

        $institucion = $meta->planEstrategico->departamento->institucion;

        $vistaMetas = true;

        return view('actividades.index', compact('actividades', 'meta', 'metas', 'usuarios', 'departamentos', 'institucion', 'vistaMetas'));
    }

    // Vista exclusiva para responsables de actividades
    public function indexResponsable()
    {
        $usuario = Auth::user();

        if ($usuario->tipo_usuario !== 'responsable_actividad') {
            return redirect()->route('home')->with('error', 'No tienes permiso para ver esta sección.');
        }

        $actividades = Actividad::with(['meta.planEstrategico', 'usuario.departamento'])
            ->where('idEncargadoActividad', $usuario->id)
            ->get();

        if ($actividades->isEmpty()) {
            return redirect()->route('home')->with('error', 'No tienes actividades asignadas.');
        }

        $metas = Meta::all(); // opcional, si la vista las usa
        $usuarios = Usuario::all(); // opcional
        $departamentos = Departamento::all(); // opcional
        $institucion = $actividades->first()->meta->planEstrategico->departamento->institucion;

        return view('actividades.index', compact('actividades', 'metas', 'usuarios', 'departamentos', 'institucion'));
    }

    // Crear actividad (solo admins o encargados)
    public function create()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan','responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear actividades.');
        }

        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();

        return view('actividades.create', compact('metas', 'usuarios', 'departamentos'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear actividades.');
        }

        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idEncargadoActividad' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|array|min:1',
            'objetivos.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'nullable|string|max:255',
        ]);

        Actividad::create([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => implode(', ', $request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'resultados_esperados' => $request->resultados_esperados,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad creada exitosamente.');
    }

    // Editar actividad (solo admins o encargados)
    public function edit(Actividad $actividad)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan','responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para editar actividades.');
        }

        $actividad = Actividad::find($actividad->id);
        $metas = Meta::all();
        $usuarios = Usuario::with('departamento')->get();
        $departamentos = Departamento::all();

        return view('actividades.edit', compact('actividad', 'metas', 'usuarios', 'departamentos'));
    }

    public function update(Request $request, string $id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar actividades.');
        }

        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idEncargadoActividad' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string|max:255',
            'objetivos' => 'required|array|min:1',
            'objetivos.*' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'resultados_esperados' => 'required|string|max:255',
            'unidad_encargada' => 'nullable|string|max:255',
        ]);

        $actividad = Actividad::find($id);
        $actividad->update([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => implode(', ', $request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'resultados_esperados' => $request->resultados_esperados,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad actualizada exitosamente.');
    }

    // Eliminar actividad (solo admins o encargados)
    public function destroy(string $id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar actividades.');
        }

        $actividad = Actividad::findOrFail($id);
        $actividad->delete();

        return redirect()->back()->with('success', 'Actividad eliminada exitosamente.');
    }
 
}
