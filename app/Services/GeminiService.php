<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    public function ask(string $prompt, array $context = []): string
    {
        try {
            $messages = [];

            foreach ($context as $msg) {
                $messages[] = [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? ''
                ];
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
            ]);

            if (!$response->successful()) {
                Log::error('Error HTTP al llamar a OpenAI:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return 'Error al conectar con OpenAI.';
            }

            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'No se obtuvo respuesta.';

        } catch (\Throwable $e) {
            Log::error('OpenAIService Exception: ' . $e->getMessage());
            return 'Error en la conexión con OpenAI.';
        }
    }
}