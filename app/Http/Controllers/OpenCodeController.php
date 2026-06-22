<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OpenCodeController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'context' => 'nullable|string',
        ]);

        $message = $request->message;
        $context = $request->context ?? '';

        $gemini = app(\App\Services\GeminiService::class);
        $response = $gemini->chat($message, $context);

        if (!$response) {
            $response = $this->mockChatResponse($message);
        }

        return response()->json(['response' => $response]);
    }

    private function mockChatResponse(string $message): string
    {
        $message = strtolower($message);

        if (str_contains($message, 'olá') || str_contains($message, 'oi')) {
            return 'Olá! Como posso ajudar você com seus estudos ou código hoje?';
        }

        if (str_contains($message, 'php') || str_contains($message, 'laravel')) {
            return 'Estou vendo que você está trabalhando com PHP/Laravel! Este framework usa MVC, e você pode criar rotas em `routes/web.php`, controllers em `app/Http/Controllers/` e views em `resources/views/`. Precisa de ajuda com algo específico?';
        }

        if (str_contains($message, 'quiz') || str_contains($message, 'questão')) {
            return 'Para criar um quiz, vá em Estudos > Quizzes e clique em "Gerar Quiz". Você pode gerar questões a partir de um material de estudo já cadastrado.';
        }

        if (str_contains($message, 'slide') || str_contains($message, 'apresentação')) {
            return 'Você pode gerar slides automaticamente a partir de materiais de estudo! Vá em Estudos > Slides e clique em "Gerar Slides".';
        }

        return 'Entendi sua pergunta sobre "' . $message . '". Por favor, forneça mais contexto ou detalhes sobre o que você precisa, e ficarei feliz em ajudar com seus estudos ou código!';
    }

    public function index()
    {
        $basePath = base_path();
        $files = $this->getDirectoryStructure($basePath);
        return view('study.opencode.index', compact('files', 'basePath'));
    }

    public function browse(Request $request)
    {
        $path = $request->get('path', base_path());
        $realPath = realpath($path);

        if (!$realPath || !str_starts_with($realPath, base_path())) {
            abort(403);
        }

        if (is_dir($realPath)) {
            $files = $this->getDirectoryStructure($realPath);
            return response()->json(['type' => 'dir', 'files' => $files]);
        }

        if (is_file($realPath)) {
            $content = File::get($realPath);
            $extension = pathinfo($realPath, PATHINFO_EXTENSION);
            return response()->json([
                'type' => 'file',
                'content' => $content,
                'extension' => $extension,
                'filename' => basename($realPath),
            ]);
        }

        abort(404);
    }

    public function save(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'content' => 'required|string',
        ]);

        $realPath = realpath($request->path);
        if (!$realPath || !str_starts_with($realPath, base_path())) {
            return response()->json(['error' => 'Permissão negada'], 403);
        }

        File::put($realPath, $request->content);

        return response()->json(['success' => true]);
    }

    private function getDirectoryStructure(string $dir): array
    {
        $items = File::directories($dir);
        $files = File::files($dir);
        $result = [];

        $ignoreDirs = ['vendor', 'node_modules', '.git', 'storage', 'bootstrap/cache'];

        foreach ($items as $item) {
            $name = basename($item);
            if (in_array($name, $ignoreDirs)) continue;
            $result[] = [
                'name' => $name,
                'path' => $item,
                'type' => 'directory',
            ];
        }

        foreach ($files as $file) {
            $result[] = [
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'type' => 'file',
                'size' => $file->getSize(),
            ];
        }

        usort($result, fn($a, $b) => $a['type'] === $b['type']
            ? strcmp($a['name'], $b['name'])
            : ($a['type'] === 'directory' ? -1 : 1));

        return $result;
    }
}
