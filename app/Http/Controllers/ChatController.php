<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ IMPORTANTE
use App\Services\GeminiService;
use App\Models\User;
use App\Models\Meta;
use App\Models\Institucion;
use App\Models\Departamento;
use App\Models\Actividad;
use App\Models\HistorialSesion;
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

        // ✅ 1. Obtener usuario autenticado
        $usuario = Auth::user();
        if (!$usuario) {
            return response()->json([
                'status' => 'error',
                'answer' => 'No hay un usuario autenticado.',
            ]);
        }

        // ✅ 2. Crear un pequeño contexto solo del usuario
        $userContext = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre_usuario ?? $usuario->name ?? 'Sin nombre',
            'email' => $usuario->email,
            'rol' => $usuario->tipo_usuario ?? 'desconocido',
            'idInstitucion' => $usuario->idInstitucion ?? null,
            'idDepartamento' => $usuario->idDepartamento ?? null,
        ];

        /**
         * 🧠 3. Historial de conversación (se guarda en sesión)
         */
        $history = session('chat_history', []);

        // Si es la primera vez, agregamos la "personalidad"
        if (empty($history)) {
            $history[] = [
                'role' => 'system',
                'content' => 'Eres un asistente técnico amigable. 
                Respondes con claridad, explicas cuando es necesario 
                y hablas de forma natural, sin sonar robótico. 
                Si el usuario pregunta sobre datos, interpreta la información 
                y resume lo relevante.'
            ];
        }

        // ✅ 4. Agregamos info del usuario autenticado al contexto de la IA
        $history[] = [
            'role' => 'system',
            'content' => 'El usuario autenticado es: ' . json_encode($userContext)
        ];

        /**
         * ✅ 5. Adjuntar los datos de la BD (como ya lo tenías)
         */
        $users = User::all(['id', 'name', 'email'])->toArray();
        $usuarios = Usuario::all(['id', 'nombre_usuario', 'email', 'tipo_usuario', 'idInstitucion', 'idDepartamento'])->toArray();
        $metas = Meta::all()->toArray();
        $instituciones = Institucion::all()->toArray();
        $departamentos = Departamento::all()->toArray();
        $actividades = Actividad::all()->toArray();
        $planes = PlanEstrategico::all()->toArray();
        $resultados = Resultado::all()->toArray();
        $sesiones = HistorialSesion::all()->toArray();

        $context = json_encode([
            'users' => $users,
            'usuarios' => $usuarios,
            'metas' => $metas,
            'instituciones' => $instituciones,
            'departamentos' => $departamentos,
            'actividades' => $actividades,
            'planes_estrategicos' => $planes,
            'resultados' => $resultados,
            'sesiones' => $sesiones,
        ]);

        $history[] = [
            'role' => 'system',
            'content' => 'Estos son los registros disponibles: ' . $context
        ];

        /**
         * ✅ 6. Agregar el mensaje actual al historial
         */
        $history[] = [
            'role' => 'user',
            'content' => $message
        ];

        /**
         * ✅ 7. Llamar a Gemini
         */
        $answer = $gemini->ask($message, $history);

        /**
         * ✅ 8. Guardar respuesta en el historial
         */
        $history[] = [
            'role' => 'assistant',
            'content' => $answer
        ];

        session(['chat_history' => $history]);

        return response()->json([
            'status' => 'ok',
            'answer' => $answer,
        ]);
    }
}
