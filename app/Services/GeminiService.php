<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->model = env('GEMINI_MODEL', 'gemini-2.0-flash');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function ask(string $question, array $history = []): string
    {
        try {
            // Combinar historial y pregunta en un solo bloque de texto
            $finalText = '';

            foreach ($history as $msg) {
                $role = strtoupper($msg['role'] ?? 'USER');
                $finalText .= "[{$role}] {$msg['content']}\n";
            }

            $finalText .= "[USER] {$question}";

            // Estructura esperada por generateContent
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $finalText]
                        ]
                    ]
                ]
            ];

            Log::info('Payload enviado a Gemini:', $payload);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey,
            ])->post($this->apiUrl, $payload);

            if (!$response->successful()) {
                Log::error('Error HTTP al llamar a Gemini:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return 'Error al conectar con Gemini (HTTP).';
            }

            $json = $response->json();
            Log::info('Respuesta cruda de Gemini:', $json);

            return $json['candidates'][0]['content']['parts'][0]['text']
                ?? 'No se obtuvo respuesta del modelo.';
        } catch (\Throwable $e) {
            Log::error('GeminiService Exception: ' . $e->getMessage());
            return 'Error al conectar con Gemini (excepción).';
        }
    }
}
