<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateQuiz(string $content, int $questionCount = 5): ?array
    {
        $prompt = <<<PROMPT
Com base no seguinte conteúdo acadêmico, gere {$questionCount} questões de múltipla escolha no formato JSON.

REGRAS:
- Cada questão deve ter 4 opções
- A resposta correta DEVE ser exatamente igual a uma das opções
- As questões devem testar compreensão, não apenas memorização
- Responda APENAS com o JSON, sem markdown, sem explicações

Formato JSON esperado (array):
[
  {
    "question": "texto da pergunta",
    "options": ["Opcao A", "Opcao B", "Opcao C", "Opcao D"],
    "correct_answer": "Opcao correta"
  }
]

Conteúdo:
{$content}
PROMPT;

        return $this->sendRequest($prompt);
    }

    public function generateSlides(string $content, int $slideCount = 5): ?array
    {
        $prompt = <<<PROMPT
Com base no seguinte conteúdo acadêmico, gere {$slideCount} slides de apresentação no formato JSON.

REGRAS:
- Cada slide deve ter 'title' e 'content'
- O primeiro slide deve ser uma introdução
- O último slide deve ser um resumo/conclusão
- Conteúdo didático e bem formatado
- Responda APENAS com o JSON, sem markdown, sem explicações

Formato JSON esperado (array):
[
  {"title": "Titulo do Slide", "content": "Conteudo explicativo do slide"},
  ...
]

Conteúdo:
{$content}
PROMPT;

        return $this->sendRequest($prompt);
    }

    public function generatePodcastScript(string $content): ?string
    {
        $prompt = <<<PROMPT
Com base no seguinte conteúdo acadêmico, gere um script de podcast/debate didático entre um Anfitrião e um Convidado.

REGRAS:
- Use o formato: [Anfitrião]: fala / [Convidado]: fala
- Deve parecer uma conversa natural e didática
- Aproximadamente 10-15 falas no total
- Inclua introdução, desenvolvimento e conclusão
- O convidado deve explicar conceitos de forma acessível
- Responda APENAS com o texto do script, sem markdown extra

Conteúdo:
{$content}
PROMPT;

        $response = $this->sendRawRequest($prompt);
        return $response;
    }

    public function chat(string $message, string $context = ''): ?string
    {
        $prompt = "Você é um assistente de IA integrado ao OpenCode, um ambiente de desenvolvimento e estudos.\n\n";
        if ($context) {
            $prompt .= "Contexto do código atual do usuário:\n```\n{$context}\n```\n\n";
        }
        $prompt .= "Usuário: {$message}\n\nResponda de forma útil, concisa e em português. Se for sobre código, dê exemplos práticos.";

        return $this->sendRawRequest($prompt);
    }

    private function sendRequest(string $prompt): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            $response = Http::timeout(60)
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$text) return null;

            $text = trim($text);
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);

            return json_decode($text, true);
        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function sendRawRequest(string $prompt): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }

        try {
            $response = Http::timeout(60)
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.8,
                        'maxOutputTokens' => 4096,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API error (raw)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (\Exception $e) {
            Log::error('Gemini API exception (raw)', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
