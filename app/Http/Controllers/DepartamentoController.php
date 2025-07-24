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
        $usuario = Auth::user(); // Usuario actual autenticado

        // Si es administrador, ve todo
        if ($usuario->tipo_usuario === 'administrador') {
            $departamentos = Departamento::with('encargadoDepartamento')->get();
        } else if ($usuario->tipo_usuario === 'encargado_institucion') {
            // Solo ve los departamentos de su institución
            $departamentos = Departamento::with('encargadoDepartamento')
                ->where('idInstitucion', $usuario->idInstitucion)
                ->get();
        } else {
            // Otros roles no deberían ver nada, o puedes ajustar según tu lógica
            $departamentos = collect(); // Lista vacía
        }

        // Obtener instituciones y usuarios solo si es admin (opcional)
        $instituciones = Institucion::all();
        $usuarios = Usuario::all();

        // Filtro para mostrar solo usuarios encargados de departamento que aún no estén asignados
        $usuariosParaCrear = Usuario::where('tipo_usuario', 'encargado_departamento')
            ->whereDoesntHave('departamentos')
            ->get();

        $usuariosParaEditar = Usuario::where('tipo_usuario', 'encargado_departamento')->get();

        return view('departamentos.index', compact(
            'departamentos',
            'instituciones',
            'usuarios',
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
        ]);

        // Obtener la institución del usuario que está creando el departamento
        $institucionId = Auth::user()->idInstitucion;

        $departamento = Departamento::create([
            'departamento' => $request->departamento,
            'idEncargadoDepartamento' => $request->idEncargadoDepartamento,
            'idInstitucion' => $institucionId,
        ]);

        // Asignar ese departamento al usuario encargado
        Usuario::where('id', $request->idEncargadoDepartamento)
            ->update([
                'idDepartamento' => $departamento->id,
                'idInstitucion' => $institucionId,
            ]);

        return redirect()->back()->with('success', 'Departamento creado exitosamente.');
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
            ->whereIn('idUsuario', $usuarios)
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
