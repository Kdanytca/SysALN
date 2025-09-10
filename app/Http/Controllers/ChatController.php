<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\User;
use App\Models\Meta;
use App\Models\Institucion;
use App\Models\Departamento;
use App\Models\Actividad;
use App\Models\PlanEstrategico;
use App\Models\Resultado;
use App\Models\Usuario;

class ChatController extends Controller
{
    public function send(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $request->input('message');

        // Extraer todas las tablas
        $users = User::all(['id', 'name', 'email'])->toArray();
        $usuarios = Usuario::all(['id', 'nombre_usuario', 'email', 'tipo_usuario', 'idInstitucion','idDepartamento'])->toArray();
        $metas = Meta::all()->toArray();
        $instituciones = Institucion::all()->toArray();
        $departamentos = Departamento::all()->toArray();
        $actividades = Actividad::all()->toArray();
        $planes = PlanEstrategico::all()->toArray();
        $resultados = Resultado::all()->toArray();

        // Preparar contexto completo
        $context = json_encode([
            'users' => $users,
            'usuarios' => $usuarios,
            'metas' => $metas,
            'instituciones' => $instituciones,
            'departamentos' => $departamentos,
            'actividades' => $actividades,
            'planes_estrategicos' => $planes,
            'resultados' => $resultados
        ]);

        $history = [
            ['role' => 'system', 'content' => 'Eres un asistente que responde basado en los registros proporcionados.'],
            ['role' => 'system', 'content' => "Estos son los datos de la base de datos: $context"]
        ];

        // Llamada al servicio Gemini
        $answer = $gemini->ask($message, $history);

        return response()->json([
            'status' => 'ok',
            'answer' => $answer,
        ]);
    }
}
