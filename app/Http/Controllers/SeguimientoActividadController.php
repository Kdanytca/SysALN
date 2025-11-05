<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\SeguimientoActividad;
use Illuminate\Http\Request;
use App\Models\Meta;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
            'evidencia.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,xls,ppt,pptx,zip,rar|max:5120', // Validación para las evidencias
        ]);

        // Crear el registro de seguimiento
        $seguimiento = SeguimientoActividad::create($request->only('periodo_consultar', 'idActividades', 'observaciones', 'estado'));

        // Procesar la evidencia si es que hay
        $rutasEvidencia = [];
        if ($request->hasFile('evidencia')) {
            foreach ($request->file('evidencia') as $archivo) {
                if (!$archivo) continue;

                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $nombreLimpio = Str::slug($nombreOriginal, '_'); // Reemplaza espacios y caracteres especiales
                $timestamp = now()->format('Ymd_His');
                $extension = $archivo->getClientOriginalExtension();

                $nombreArchivo = "{$nombreLimpio}_{$timestamp}.{$extension}";

                // Definir la carpeta de destino según el tipo de archivo
                if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $carpeta = 'uploads/seguimiento/imagenes';
                } else {
                    $carpeta = 'uploads/seguimiento/documentos';
                }

                $carpetaDestino = public_path($carpeta);
                if (!File::exists($carpetaDestino)) {
                    File::makeDirectory($carpetaDestino, 0755, true);
                }

                $archivo->move($carpetaDestino, $nombreArchivo);
                $rutasEvidencia[] = $carpeta . '/' . $nombreArchivo;
            }
        }

        // Actualizamos el registro de seguimiento con las rutas de la evidencia
        $seguimiento->update([
            'evidencia' => json_encode($rutasEvidencia), // Guardamos las rutas de las evidencias
        ]);

        // Obtener el ID de la actividad para redirigir
        $actividad = Actividad::findOrFail($request->idActividades);
        $idMeta = $actividad->idMetas;

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
    public function destroy(string $id)
    {
        try {
            // Permisos
            if (!in_array(Auth::user()->tipo_usuario, ['administrador', 'encargado_institucion', 'responsable_plan', 'responsable_meta'])) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar seguimientos.'], 403);
            }

            $seguimiento = SeguimientoActividad::findOrFail($id);

            // Obtener y eliminar evidencias físicas
            $evidencias = json_decode($seguimiento->evidencia, true) ?? [];

            foreach ($evidencias as $ruta) {
                if (!empty($ruta) && str_starts_with($ruta, 'uploads/seguimiento/')) {
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

            $seguimiento->delete();

            // Siempre devolver JSON
            return response()->json(['success' => true, 'message' => 'Seguimiento eliminado correctamente.']);

        } catch (\Throwable $th) {
            \Log::error('Error al eliminar seguimiento: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al eliminar el seguimiento.'
            ], 500);
        }
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
            'evidencia' => 'nullable|array',
            'evidencia.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,xls,ppt,pptx,zip,rar|max:5120',
            'eliminar_evidencia' => 'nullable|array',
        ]);

        $seguimiento = SeguimientoActividad::findOrFail($id);

        // Obtener evidencias actuales (si existen)
        $evidenciaActual = json_decode($seguimiento->evidencia, true) ?? [];
        $evidenciaAEliminar = $request->input('eliminar_evidencia', []);

        // Eliminar archivos seleccionados
        foreach ($evidenciaAEliminar as $ruta) {
            if (empty($ruta)) continue;

            $rutaCompleta = public_path($ruta);

            // Seguridad: evitar eliminar archivos fuera de la carpeta de seguimiento
            if (is_file($rutaCompleta) && str_contains($rutaCompleta, 'uploads/seguimiento')) {
                try {
                    @unlink($rutaCompleta);
                } catch (\Exception $e) {
                    \Log::warning("No se pudo eliminar el archivo: {$rutaCompleta}. Error: " . $e->getMessage());
                }
            }
        }

        // Quitar del array los archivos eliminados
        $evidenciaFinal = array_values(array_diff($evidenciaActual, $evidenciaAEliminar));

        // Subir nuevas evidencias
        $evidenciaNueva = $request->file('evidencia') ?? $request->file('evidencia_nueva');
        if ($evidenciaNueva && is_array($evidenciaNueva)) {
            foreach ($evidenciaNueva as $archivo) {
                if (!$archivo) continue;

                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $nombreLimpio = Str::slug($nombreOriginal, '_');
                $timestamp = now()->format('Ymd_His');
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = "{$nombreLimpio}_{$timestamp}.{$extension}";

                // Determinar carpeta destino según el tipo
                $carpeta = in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])
                    ? 'uploads/seguimiento/imagenes'
                    : 'uploads/seguimiento/documentos';

                $carpetaDestino = public_path($carpeta);
                if (!File::exists($carpetaDestino)) {
                    File::makeDirectory($carpetaDestino, 0755, true);
                }

                $archivo->move($carpetaDestino, $nombreArchivo);
                $evidenciaFinal[] = $carpeta . '/' . $nombreArchivo;
            }
        }

        // Actualizar campos del seguimiento
        $seguimiento->update([
            'periodo_consultar' => $request->input('periodo_consultar'),
            'idActividades' => $request->input('idActividades'),
            'observaciones' => $request->input('observaciones'),
            'estado' => $request->input('estado'),
            'evidencia' => json_encode($evidenciaFinal),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Seguimiento actualizado correctamente.');
    }

    public function guardarEvidencias(Request $request, $id)
    {
        $seguimiento = SeguimientoActividad::findOrFail($id);

        $evidenciaActual = json_decode($seguimiento->evidencia, true) ?? [];
        $evidenciaAEliminar = $request->input('eliminar_evidencia', []);

        // Eliminar archivos seleccionados
        foreach ($evidenciaAEliminar as $ruta) {
            $rutaCompleta = public_path($ruta);
            if (is_file($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        }

        $evidenciaFinal = array_values(array_diff($evidenciaActual, $evidenciaAEliminar));

        // Subir nuevas evidencias (con el mismo formato que al crear/actualizar actividades)
        if ($request->hasFile('evidencia_nueva')) {
            foreach ($request->file('evidencia_nueva') as $archivo) {
                if (!$archivo) continue;

                $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $nombreLimpio = Str::slug($nombreOriginal, '_'); // Reemplaza espacios y caracteres especiales
                $timestamp = now()->format('Ymd_His');
                $extension = $archivo->getClientOriginalExtension();

                $nombreArchivo = "{$nombreLimpio}_{$timestamp}.{$extension}";

                // Determinar la carpeta destino según el tipo
                if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $carpeta = 'uploads/seguimiento/imagenes';
                } else {
                    $carpeta = 'uploads/seguimiento/documentos';
                }

                $carpetaDestino = public_path($carpeta);
                if (!File::exists($carpetaDestino)) {
                    File::makeDirectory($carpetaDestino, 0755, true);
                }

                $archivo->move($carpetaDestino, $nombreArchivo);
                $evidenciaFinal[] = $carpeta . '/' . $nombreArchivo;
            }
        }

        $seguimiento->update(['evidencia' => json_encode($evidenciaFinal)]);

        return redirect()->back()->with('success', 'Evidencias del seguimiento actualizadas correctamente.');
    }
   
}
