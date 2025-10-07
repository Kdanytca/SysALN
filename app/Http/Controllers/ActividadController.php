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
            'nombre_actividad' => 'required|string',
            'objetivos' => 'required|array|min:1',
            'objetivos.*' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'comentario' => 'required|string',
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

        $rutasImagenes = [];

        if ($request->hasFile('imagenes')) {
            $carpetaDestino = public_path('uploads/actividades');

            if (!File::exists($carpetaDestino)) {
                File::makeDirectory($carpetaDestino, 0755, true);
            }

            foreach ($request->file('imagenes') as $imagen) {
                $nombreArchivo = Str::uuid() . '.' . $imagen->getClientOriginalExtension();
                $imagen->move($carpetaDestino, $nombreArchivo);
                $rutasImagenes[] = 'uploads/actividades/' . $nombreArchivo;
            }
        }

        Actividad::create([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => json_encode($request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'imagenes' => json_encode($rutasImagenes),
            'comentario' => $request->comentario,
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
        if (!in_array(Auth::user()->tipo_usuario, ['administrador','encargado_institucion','responsable_plan','responsable_meta'])) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar actividades.');
        }

        $meta = Meta::findOrFail($request->idMetas);
        $actividad = Actividad::findOrFail($id);

        $request->validate([
            'idMetas' => 'required|exists:metas,id',
            'idEncargadoActividad' => 'required|exists:usuarios,id',
            'nombre_actividad' => 'required|string',
            'objetivos' => 'required|array|min:1',
            'objetivos.*' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'imagenes_nuevas' => 'nullable|array',
            'imagenes_nuevas.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'eliminar_imagenes' => 'nullable|array',
            'comentario' => 'required|string',
            'unidad_encargada' => 'nullable|string',
        ]);

        // Validar rango de fechas
        if ($request->fecha_inicio < $meta->fecha_inicio) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio de la actividad no puede ser anterior a la de la meta.']);
        }
        if ($request->fecha_fin > $meta->fecha_fin) {
            return back()->withErrors(['fecha_fin' => 'La fecha de fin de la actividad no puede ser posterior a la de la meta.']);
        }

        // Imágenes actuales guardadas
        $imagenesActuales = json_decode($actividad->imagenes, true) ?? [];
        $imagenesAEliminar = $request->input('eliminar_imagenes', []);

        // Eliminar físicamente las imágenes seleccionadas
        foreach ($imagenesAEliminar as $ruta) {
            if (empty($ruta)) continue;

            $rutaCompleta = public_path($ruta);

            // Evitar intentar borrar la carpeta public o rutas inválidas
            if (is_file($rutaCompleta) && str_contains($rutaCompleta, 'uploads/actividades')) {
                try {
                    @unlink($rutaCompleta);
                } catch (\Exception $e) {
                    \Log::warning("No se pudo eliminar la imagen: {$rutaCompleta}. Error: " . $e->getMessage());
                }
            }
        }

        // Construir el nuevo arreglo de imágenes
        $imagenesFinales = array_values(array_diff($imagenesActuales, $imagenesAEliminar));

        // Subir nuevas imágenes (si las hay)
        $imagenesNuevas = $request->file('imagenes_nuevas');

        if ($imagenesNuevas && is_array($imagenesNuevas)) {
            $carpetaDestino = public_path('uploads/actividades');

            if (!File::exists($carpetaDestino)) {
                File::makeDirectory($carpetaDestino, 0755, true);
            }

            foreach ($imagenesNuevas as $imagen) {
                if (!$imagen) continue;

                $nombreArchivo = Str::uuid() . '.' . $imagen->getClientOriginalExtension();
                $imagen->move($carpetaDestino, $nombreArchivo);
                $imagenesFinales[] = 'uploads/actividades/' . $nombreArchivo;
            }
        }

        // Si el usuario eliminó todas las imágenes (ya no quedan ni nuevas ni actuales)
        if (empty($imagenesFinales)) {
            $actividad->imagenes = json_encode([]); // Guardar campo vacío
        } else {
            $actividad->imagenes = json_encode($imagenesFinales);
        }

        // Actualizar el resto de los campos
        $actividad->update([
            'idMetas' => $request->idMetas,
            'idEncargadoActividad' => $request->idEncargadoActividad,
            'nombre_actividad' => $request->nombre_actividad,
            'objetivos' => json_encode($request->objetivos),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'imagenes' => $actividad->imagenes,
            'comentario' => $request->comentario,
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

        // Obtener las imágenes asociadas a la actividad
        $imagenes = json_decode($actividad->imagenes, true) ?? [];

        // Eliminar físicamente cada imagen
        foreach ($imagenes as $ruta) {
            // Validar ruta antes de eliminar
            if (!empty($ruta) && str_starts_with($ruta, 'uploads/actividades/')) {
                $rutaCompleta = public_path($ruta);

                if (is_file($rutaCompleta) && file_exists($rutaCompleta)) {
                    try {
                        unlink($rutaCompleta);
                    } catch (\Exception $e) {
                        \Log::error("❌ No se pudo eliminar la imagen: {$rutaCompleta}. Error: " . $e->getMessage());
                    }
                }
            }
        }

        // Eliminar la actividad de la base de datos
        $actividad->delete();

        return redirect()->back()->with('success', 'Actividad eliminada exitosamente.');
    }
}
