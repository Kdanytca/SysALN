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
        $this->model = env('GEMINI_MODEL', 'gemini-2.0-flash'); // ajusta si usas otro
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Envía la pregunta + historial a Gemini y devuelve la respuesta como string.
     */
    public function ask(string $question, array $history = []): string
    {
        try {
            // Combinar historial + pregunta actual en un solo texto
            $fullText = '';
            foreach ($history as $m) {
                $fullText .= ($m['role'] === 'system' ? '[SYSTEM] ' : '[USER] ') . $m['content'] . "\n";
            }
            $fullText .= "[USER] " . $question;

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullText]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey,
            ])->post($this->apiUrl, $payload);

            $json = $response->json();

            // Log para debug si quieres revisar la respuesta completa
            Log::info('Gemini raw response', $json);

            // Extraer solo el texto de la IA para enviar al chat
            return $json['candidates'][0]['content']['parts'][0]['text'] ?? 'No se obtuvo respuesta';

        } catch (\Throwable $e) {
            Log::error('GeminiService error: ' . $e->getMessage());
            return 'Error al conectar con Gemini.';
        }
    }
}
