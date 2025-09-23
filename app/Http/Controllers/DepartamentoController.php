<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Institucion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DepartamentoController extends Controller
{
    // Muestra la lista de departamentos
    public function index()
    {
        $user = Auth::user();

        if ($user->tipo_usuario === 'encargado_departamento') {
            // Solo su departamento
            $departamentos = Departamento::with('institucion')
                ->where('id', $user->idDepartamento)
                ->get();

            $institucion = $departamentos->first()?->institucion;

            // Enviar un array con la institución para que la vista pueda iterar
            $instituciones = $institucion ? collect([$institucion]) : collect();

            $usuarios = [];
            $usuariosParaCrear = [];
            $usuariosParaEditar = [];
        } else {
            // Admin u otros roles
            $departamentos = Departamento::with('institucion')->get();
            $institucion = null; // Opcional
            $instituciones = Institucion::all();
            $usuarios = Usuario::all();
            $usuariosParaCrear = [];
            $usuariosParaEditar = [];
        }

        return view('departamentos.index', compact(
            'departamentos',
            'institucion',
            'instituciones',
            'usuarios',
            'usuariosParaCrear',
            'usuariosParaEditar'
        ));
    }



    // Muestra la lista de departamentos filtrados por institución
    public function indexPorInstitucion(Institucion $institucion)
    {
        $departamentos = $institucion->departamentos()->with('institucion')->get();
        $instituciones = Institucion::all();

        // Variables necesarias para la vista
        $usuariosParaCrear = Usuario::whereIn('tipo_usuario', ['encargado_departamento', 'encargado_institucion'])
            ->whereDoesntHave('departamentos')
            ->whereHas('institucion', function($query) use ($institucion) {
                $query->where('id', $institucion->id);
            })
            ->get();

        $usuariosParaEditar = [];

        foreach ($departamentos as $departamento) {
            $encargadoActual = $departamento->encargadoDepartamento; // Ajusta la relación si es otro nombre

            $usuariosDisponibles = Usuario::where('tipo_usuario', 'encargado_departamento', 'encargado_institucion')
                ->where(function ($query) use ($encargadoActual) {
                    $query->whereDoesntHave('departamentos');
                    if ($encargadoActual) {
                        $query->orWhere('id', $encargadoActual->id);
                    }
                })
                ->whereHas('departamentos', function($query) use ($departamento) {
                    $query->where('idInstitucion', $departamento->idInstitucion);
                })
                ->get();

            $usuariosParaEditar[$departamento->id] = $usuariosDisponibles;
        }

        return view('departamentos.index', compact(
            'departamentos',
            'institucion',
            'instituciones',
            'usuariosParaCrear',
            'usuariosParaEditar'
        ));
    }

    // Se encarga de crear un nuevo departamento
    public function store(Request $request)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'idEncargadoDepartamento' => [
                'required',
                'exists:usuarios,id',
                Rule::unique('departamentos', 'idEncargadoDepartamento')
            ],
            'idInstitucion' => 'required|exists:instituciones,id',
        ]);

        $departamento = Departamento::create([
            'departamento' => $request->departamento,
            'idEncargadoDepartamento' => $request->idEncargadoDepartamento,
            'idInstitucion' => $request->idInstitucion,
        ]);

        // Asignar ese departamento al usuario encargado
        Usuario::where('id', $request->idEncargadoDepartamento)
            ->update([
                'idDepartamento' => $departamento->id,
                'idInstitucion' => $request->idInstitucion,
            ]);

        // Aquí detectamos si viene desde fetch/ajax
        if ($request->expectsJson()) {
            return response()->json([
                'departamento' => $departamento
            ]);
        }

        // Si es petición normal (desde formulario no-AJAX)
        return redirect()->back()->with('success', 'Departamento creado exitosamente.');
    }


    //mostrando todos los departamentos
    public function todos()
    {
        $departamentos = Departamento::with('institucion')->get(); // si tienes relación con Institución
        return view('departamentos.index_general', compact('departamentos'));
    }

    // Se encarga de editar un departamento
    public function update(Request $request, string $id)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'idEncargadoDepartamento' => 'required|string|max:45',
            'idInstitucion' => 'required|exists:instituciones,id',
        ]);

        $departamento = Departamento::findOrFail($id);
        $oldNombre = $departamento->getOriginal('departamento');

        $departamento->update([
            'departamento' => $request->departamento,
            'idEncargadoDepartamento' => $request->idEncargadoDepartamento,
            'idInstitucion' => $request->idInstitucion,
        ]);

        // Obtener todos los usuarios de ese departamento
        $usuarios = DB::table('usuarios')->where('idDepartamento', $departamento->id)->pluck('id');

        // Actualizar actividades cuya unidad_encargada coincida con el nombre antiguo del departamento
        DB::table('actividades')
            ->whereIn('idEncargadoActividad', $usuarios)
            ->where('unidad_encargada', $oldNombre)
            ->update(['unidad_encargada' => $departamento->departamento]);

        return redirect()->back()->with('success', 'Departamento actualizado correctamente.');
    }

    // Elimina un departamento
    public function destroy(string $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return redirect()->back()->with('success', 'Departamento eliminado correctamente.');
    }
}
