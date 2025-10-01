<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstitucionController extends Controller
{
    public function index()
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para ver esta sección.');
        }

        $instituciones = Institucion::with('encargadoInstitucion')->get();
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();

        $usuariosParaCrear = Usuario::where('tipo_usuario', 'encargado_institucion')
            ->whereDoesntHave('instituciones')
            ->get();

        $usuariosParaEditar = [];

        foreach ($instituciones as $institucion) {
            $encargadoActual = $institucion->encargadoInstitucion;

            $usuariosDisponibles = Usuario::where('tipo_usuario', 'encargado_institucion')
                ->where(function ($query) use ($encargadoActual) {
                    $query->whereDoesntHave('instituciones');

                    if ($encargadoActual) {
                        $query->orWhere('id', $encargadoActual->id);
                    }
                })
                ->get();

            $usuariosParaEditar[$institucion->id] = $usuariosDisponibles;
        }

        return view('instituciones.index', compact('instituciones', 'usuarios', 'usuariosParaCrear', 'usuariosParaEditar', 'departamentos'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para crear instituciones.');
        }

        $request->validate([
            'nombre_institucion' => 'required|string',
            'tipo_institucion' => 'required|string',
            'idEncargadoInstitucion' => [
                'required',
                'exists:usuarios,id',
                Rule::unique('instituciones', 'idEncargadoInstitucion')
            ],
        ]);

        $institucion = Institucion::create([
            'nombre_institucion' => $request->nombre_institucion,
            'tipo_institucion' => $request->tipo_institucion,
            'idEncargadoInstitucion' => $request->idEncargadoInstitucion,
        ]);

        Usuario::where('id', $request->idEncargadoInstitucion)
            ->update(['idInstitucion' => $institucion->id]);

        return redirect()->route('instituciones.index')->with('success', 'Institución registrada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar instituciones.');
        }

        $request->validate([
            'nombre_institucion' => 'required|string',
            'tipo_institucion' => 'required|string',
            'idEncargadoInstitucion' => 'required|exists:usuarios,id',
        ]);

        $institucion = Institucion::findOrFail($id);

        // Guardar el encargado anterior antes de actualizar
        $encargadoAnteriorId = $institucion->idEncargadoInstitucion;

        // Actualizar la institución
        $institucion->update([
            'nombre_institucion' => $request->nombre_institucion,
            'tipo_institucion' => $request->tipo_institucion,
            'idEncargadoInstitucion' => $request->idEncargadoInstitucion,
        ]);

        // Desasignar el encargado anterior, si cambió
        if ($encargadoAnteriorId !== $request->idEncargadoInstitucion) {
            Usuario::where('id', $encargadoAnteriorId)
                ->update(['idInstitucion' => null]);
        }

        // Asignar la institución al nuevo encargado
        Usuario::where('id', $request->idEncargadoInstitucion)
            ->update(['idInstitucion' => $institucion->id]);

        return redirect()->route('instituciones.index')->with('success', 'Institución actualizada correctamente.');
    }

    public function destroy($id)
    {
        if (Auth::user()->tipo_usuario === 'encargado_institucion') {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar instituciones.');
        }

        $institucion = Institucion::findOrFail($id);
        $institucion->delete();

        return redirect()->route('instituciones.index')->with('success', 'Institución eliminada correctamente.');
    }

    public function eliminarInstitucionConUsuarios($id)
    {
        $institucion = Institucion::with('departamentos.planes.metas.actividades')->findOrFail($id);

        DB::transaction(function () use ($institucion) {
            $departamentosIds = $institucion->departamentos->pluck('id');

            // 1. Usuarios de metas
            $usuariosMetas = DB::table('metas')
                ->join('planes_estrategicos', 'metas.idPlanEstrategico', '=', 'planes_estrategicos.id')
                ->whereIn('planes_estrategicos.idDepartamento', $departamentosIds)
                ->whereNotNull('idEncargadoMeta')
                ->pluck('idEncargadoMeta');

            // 2. Usuarios de actividades
            $usuariosActividades = DB::table('actividades')
                ->join('metas', 'actividades.idMetas', '=', 'metas.id')
                ->join('planes_estrategicos', 'metas.idPlanEstrategico', '=', 'planes_estrategicos.id')
                ->whereIn('planes_estrategicos.idDepartamento', $departamentosIds)
                ->whereNotNull('actividades.idEncargadoActividad')
                ->pluck('actividades.idEncargadoActividad');

            // 3. Usuarios encargados de departamentos
            $usuariosEncargadosDepartamentos = $institucion->departamentos
                ->pluck('idEncargadoDepartamento')
                ->filter(); // quita los null

            // 4. Usuario encargado de la institución
            $usuarioEncargadoInstitucion = $institucion->idEncargadoInstitucion
                ? collect([$institucion->idEncargadoInstitucion])
                : collect([]);

            // 5. Usuario responsable del plan
            $usuariosResponsablesPlanes = DB::table('planes_estrategicos')
                ->whereIn('idDepartamento', $departamentosIds)
                ->whereNotNull('idUsuario')  // Asegurarse de que haya un responsable asignado
                ->pluck('idUsuario');

            // 6. Unir todos los usuarios a eliminar
            $usuariosAEliminar = $usuariosMetas
                ->merge($usuariosActividades)
                ->merge($usuariosEncargadosDepartamentos)
                ->merge($usuarioEncargadoInstitucion)
                ->merge($usuariosResponsablesPlanes)  // Agregar responsables de planes
                ->unique();

            // 7. Eliminar usuarios
            DB::table('usuarios')->whereIn('id', $usuariosAEliminar)->delete();

            // 8. Eliminar resultados
            DB::table('resultados')->whereIn('idPlanEstrategico', function ($query) use ($departamentosIds) {
                $query->select('id')->from('planes_estrategicos')->whereIn('idDepartamento', $departamentosIds);
            })->delete();

            // 9. Eliminar actividades
            DB::table('actividades')->whereIn('idMetas', function ($query) use ($departamentosIds) {
                $query->select('id')->from('metas')->whereIn('idPlanEstrategico', function ($query) use ($departamentosIds) {
                    $query->select('id')->from('planes_estrategicos')->whereIn('idDepartamento', $departamentosIds);
                });
            })->delete();

            // 10. Eliminar metas
            DB::table('metas')->whereIn('idPlanEstrategico', function ($query) use ($departamentosIds) {
                $query->select('id')->from('planes_estrategicos')->whereIn('idDepartamento', $departamentosIds);
            })->delete();

            // 11. Eliminar planes
            DB::table('planes_estrategicos')->whereIn('idDepartamento', $departamentosIds)->delete();

            // 12. Eliminar departamentos
            $institucion->departamentos()->delete();

            // 13. Eliminar institución
            $institucion->delete();
        });

        return redirect()->route('instituciones.index');
    }

    public function ver($id)
    {
        $usuario = Auth::user();

        if ($usuario->tipo_usuario !== 'encargado_institucion' || $usuario->idInstitucion != $id) {
            return redirect()->back()->with('error', 'No tienes permiso para ver esta institución.');
        }

        $instituciones = Institucion::where('id', $id)->get();
        $usuariosParaCrear = Usuario::all();
        $usuariosParaEditar = Usuario::all();

        return view('instituciones.index', compact('instituciones', 'usuariosParaCrear', 'usuariosParaEditar'));
    }
}
