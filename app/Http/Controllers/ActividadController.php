<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\Usuario;
use App\Models\Departamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ActividadController extends Controller
{
    // Vista general (solo admins o encargados)
    public function index()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
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
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $meta->load('planEstrategico.departamento.institucion');
        $actividades = $meta->actividades()->with('meta')->get();

        $institucion = $meta->planEstrategico->departamento->institucion;

        // Filtrar solo las metas de esa institución
        $metas = Meta::whereHas('planEstrategico.departamento', function ($query) use ($institucion) {
            $query->where('idInstitucion', $institucion->id);
        })->get();

        $usuarios = Usuario::where('idInstitucion', $meta->planEstrategico->departamento->idInstitucion)
            ->whereIn('tipo_usuario', ['responsable_actividad', 'encargado_institucion'])
            ->get();

        $departamentos = Departamento::where('idInstitucion', $institucion->id)->get();

        // Construir disponibles solo desde la meta actual
        $actividadesDisponibles = collect();
        if (!empty($meta->nombre_actividades)) {
            $actividadesArray = json_decode($meta->nombre_actividades, true);
            if (is_array($actividadesArray)) {
                foreach ($actividadesArray as $actividadMeta) {
                    $actividadMeta = trim($actividadMeta);
                    if ($actividadMeta !== '') {
                        $actividadesDisponibles->push($actividadMeta);
                    }
                }
            }
        }

        $vistaMetas = true;

        return view('actividades.index', compact('actividades', 'meta', 'metas', 'usuarios', 'departamentos', 'institucion', 'vistaMetas', 'actividadesDisponibles'));
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
        $meta = $metas->first();

        // Construir disponibles desde TODAS las metas
        $actividadesDisponibles = collect();
        if (!empty($meta->nombre_actividades)) {
            $actividadesArray = json_decode($meta->nombre_actividades, true);
            if (is_array($actividadesArray)) {
                foreach ($actividadesArray as $actividadMeta) {
                    $actividadMeta = trim($actividadMeta);
                    if ($actividadMeta !== '') {
                        $actividadesDisponibles->push($actividadMeta);
                    }
                }
            }
        }

        return view('actividades.index', compact('actividades', 'metas', 'usuarios', 'departamentos', 'institucion', 'actividadesDisponibles', 'meta'));
    }

    // Crear actividad (solo admins o encargados)
    public function create()
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear actividades.');
        }

        $metas = Meta::all();
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();

        return view('actividades.create', compact('metas', 'usuarios', 'departamentos'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para crear actividades.');
        }

        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idEncargadoActividad' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string',
            'objetivos' => 'nullable|array|min:1',
            'objetivos.*' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'evidencia.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,xls,ppt,pptx,zip,rar|max:5120',
            'comentario' => 'nullable|string',
            'unidad_encargada' => 'nullable|string',
        ]);

        $meta = Meta::find($request->idMetas);

        // Verificar que las fechas de la actividad estén dentro del rango de la meta
        if ($request->fecha_inicio < $meta->fecha_inicio) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio de la actividad no puede ser anterior a la de la meta.']);
        }
        if ($request->fecha_fin > $meta->fecha_fin) {
            return back()->withErrors(['fecha_fin' => 'La fecha de fin de la actividad no puede ser posterior a la de la meta.']);
        }

        $rutasEvidencia = [];

        if ($request->hasFile('evidencia')) {
            foreach ($request->file('evidencia') as $archivo) {
                if (!$archivo) continue;

                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $nombreLimpio = Str::slug($nombreOriginal, '_'); // reemplaza espacios y caracteres especiales
                $timestamp = now()->format('Ymd_His');
                $extension = $archivo->getClientOriginalExtension();

                $nombreArchivo = "{$nombreLimpio}_{$timestamp}.{$extension}";

                // Determinar la carpeta destino según el tipo
                if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $carpeta = 'uploads/actividades/imagenes';
                } else {
                    $carpeta = 'uploads/actividades/documentos';
                }

                $carpetaDestino = public_path($carpeta);
                if (!File::exists($carpetaDestino)) {
                    File::makeDirectory($carpetaDestino, 0755, true);
                }

                $archivo->move($carpetaDestino, $nombreArchivo);
                $rutasEvidencia[] = $carpeta . '/' . $nombreArchivo;
            }
        }

        Actividad::create([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => json_encode($request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'evidencia' => json_encode($rutasEvidencia),
            'comentario' => $request->comentario,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad creada exitosamente.');
    }

    // Editar actividad (solo admins o encargados)
    public function edit(Actividad $actividad)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
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
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar actividades.');
        }

        $meta = Meta::findOrFail($request->idMetas);
        $actividad = Actividad::findOrFail($id);

        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idEncargadoActividad' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string',
            'objetivos' => 'nullable|array|min:1',
            'objetivos.*' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'evidencia_nueva' => 'nullable|array',
            'evidencia_nueva.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,xls,ppt,pptx,zip,rar|max:5120',
            'eliminar_evidencia' => 'nullable|array',
            'comentario' => 'nullable|string',
            'unidad_encargada' => 'nullable|string',
        ]);

        // Validar rango de fechas
        if ($request->fecha_inicio < $meta->fecha_inicio) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio de la actividad no puede ser anterior a la de la meta.']);
        }
        if ($request->fecha_fin > $meta->fecha_fin) {
            return back()->withErrors(['fecha_fin' => 'La fecha de fin de la actividad no puede ser posterior a la de la meta.']);
        }

        $evidenciaActual = json_decode($actividad->evidencia, true) ?? [];
        $evidenciaAEliminar = $request->input('eliminar_evidencia', []);

        foreach ($evidenciaAEliminar as $ruta) {
            if (empty($ruta)) continue;

            $rutaCompleta = public_path($ruta);

            // Evitar eliminar archivos fuera del directorio previsto
            if (is_file($rutaCompleta) && str_contains($rutaCompleta, 'uploads/actividades')) {
                try {
                    @unlink($rutaCompleta);
                } catch (\Exception $e) {
                    \Log::warning("No se pudo eliminar el archivo: {$rutaCompleta}. Error: " . $e->getMessage());
                }
            }
        }

        $evidenciaFinal = array_values(array_diff($evidenciaActual, $evidenciaAEliminar));

        $evidenciaNueva = $request->file('evidencia_nueva');
        if ($evidenciaNueva && is_array($evidenciaNueva)) {
            foreach ($evidenciaNueva as $archivo) {
                if (!$archivo) continue;

                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $nombreLimpio = Str::slug($nombreOriginal, '_'); // reemplaza espacios y caracteres especiales
                $timestamp = now()->format('Ymd_His');
                $extension = $archivo->getClientOriginalExtension();

                $nombreArchivo = "{$nombreLimpio}_{$timestamp}.{$extension}";

                $carpeta = in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])
                    ? 'uploads/actividades/imagenes'
                    : 'uploads/actividades/documentos';

                $carpetaDestino = public_path($carpeta);
                if (!File::exists($carpetaDestino)) {
                    File::makeDirectory($carpetaDestino, 0755, true);
                }

                $archivo->move($carpetaDestino, $nombreArchivo);
                $evidenciaFinal[] = $carpeta . '/' . $nombreArchivo;
            }
        }

        $actividad->evidencia = json_encode($evidenciaFinal);

        // Actualizar el resto de los campos
        $actividad->update([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => json_encode($request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'evidencia' => $actividad->evidencia,
            'comentario' => $request->comentario,
            'unidad_encargada' => $request->unidad_encargada,
        ]);

        return redirect()->back()->with('success', 'Actividad actualizada exitosamente.');
    }

    // Eliminar actividad (solo admins o encargados)
    public function destroy(string $id)
    {
        if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar actividades.');
        }

        $actividad = Actividad::findOrFail($id);

        // Obtener las evidencias (imágenes y documentos)
        $evidencias = json_decode($actividad->evidencia, true) ?? [];

        // Eliminar físicamente cada archivo
        foreach ($evidencias as $ruta) {
            if (!empty($ruta) && str_starts_with($ruta, 'uploads/actividades/')) {
                $rutaCompleta = public_path($ruta);

                if (is_file($rutaCompleta) && file_exists($rutaCompleta)) {
                    try {
                        unlink($rutaCompleta);
                    } catch (\Exception $e) {
                        \Log::error("❌ No se pudo eliminar el archivo: {$rutaCompleta}. Error: " . $e->getMessage());
                    }
                }
            }
        }

        // Eliminar la actividad de la base de datos
        $actividad->delete();

        return redirect()->back()->with('success', 'Actividad eliminada exitosamente.');
    }
    public function rangoFechas($id)
    {
        $actividad = \App\Models\Actividad::findOrFail($id);

        return response()->json([
            'fecha_inicio' => $actividad->fecha_inicio,
            'fecha_fin' => $actividad->fecha_fin,
        ]);
    }
}
