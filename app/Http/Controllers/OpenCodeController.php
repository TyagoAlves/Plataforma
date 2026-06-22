<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

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

        $executionResult = $this->executeCodeFromMessage($message);
        if ($executionResult) {
            return response()->json(['response' => $executionResult]);
        }

        $gemini = app(\App\Services\GeminiService::class);
        $response = $gemini->chat($message, $context);

        if (!$response) {
            $response = $this->mockChatResponse($message);
        }

        return response()->json(['response' => $response]);
    }

    public function localChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = strtolower(trim($request->message));

        $executionResult = $this->executeCodeFromMessage($message);
        if ($executionResult) {
            return response()->json(['response' => $executionResult]);
        }

        $response = $this->localChatResponse($message);

        return response()->json(['response' => $response]);
    }

    private function localChatResponse(string $message): string
    {
        $keywords = [
            'ola' => 'Olá! Como posso ajudar você com seus estudos ou código hoje?',
            'oi' => 'Olá! Como posso ajudar você com seus estudos ou código hoje?',
            'bom dia' => 'Bom dia! Como posso ajudar você com seus estudos ou código hoje?',
            'boa tarde' => 'Boa tarde! Como posso ajudar você com seus estudos ou código hoje?',
            'boa noite' => 'Boa noite! Como posso ajudar você com seus estudos ou código hoje?',
            'php' => 'Estou vendo que você está trabalhando com PHP/Laravel! Este framework usa MVC, e você pode criar rotas em `routes/web.php`, controllers em `app/Http/Controllers/` e views em `resources/views/`. Precisa de ajuda com algo específico?',
            'laravel' => 'Estou vendo que você está trabalhando com PHP/Laravel! Este framework usa MVC, e você pode criar rotas em `routes/web.php`, controllers em `app/Http/Controllers/` e views em `resources/views/`. Precisa de ajuda com algo específico?',
            'quiz' => 'Para criar um quiz, vá em Estudos > Quizzes e clique em "Gerar Quiz". Você pode gerar questões a partir de um material de estudo já cadastrado.',
            'simulado' => 'Para criar um quiz, vá em Estudos > Quizzes e clique em "Gerar Quiz". Você pode gerar questões a partir de um material de estudo já cadastrado.',
            'slide' => 'Você pode gerar slides automaticamente a partir de materiais de estudo! Vá em Estudos > Slides e clique em "Gerar Slides".',
            'apresentação' => 'Você pode gerar slides automaticamente a partir de materiais de estudo! Vá em Estudos > Slides e clique em "Gerar Slides".',
            'podcast' => 'Que legal! Você pode gerar podcasts automaticamente a partir dos seus materiais de estudo pelo botão "Gerar Podcast" na página do material.',
            'ajuda' => 'Comandos disponíveis:\n- "ola" - saudação\n- "php/laravel" - ajuda com código\n- "quiz/simulado" - criar quizzes\n- "slide/apresentação" - criar slides\n- "podcast" - criar podcasts\n- "teste/soma/roda/executa" - executar código\n- "processos" - gerenciar processos',
            'obrigado' => 'Por nada! Estou aqui para ajudar. Continue estudando que o conhecimento vem! :)',
        ];

        foreach ($keywords as $word => $response) {
            if (str_contains($message, $word)) {
                return $response;
            }
        }

        return 'Entendi sua pergunta sobre "' . $message . '". Por favor, forneça mais contexto ou detalhes sobre o que você precisa, e ficarei feliz em ajudar com seus estudos ou código!';
    }

    public function hasExplicitCodeBlock(string $message): bool
    {
        return preg_match('/```[\s\S]*?```/', $message) === 1;
    }

    public function detectIntent(string $message): ?string
    {
        $message = strtolower($message);

        $intents = [
            'teste' => 'test',
            'soma' => 'math',
            'calcule' => 'math',
            'calcula' => 'math',
            'matemática' => 'math',
            'roda' => 'execute',
            'executa' => 'execute',
            'rode' => 'execute',
            'loop' => 'loop',
            'array' => 'array',
            'tabuada' => 'multiplication',
            'media' => 'average',
            'imc' => 'imc',
            'fatorial' => 'factorial',
            'fibonacci' => 'fibonacci',
            'ordenar' => 'sort',
            'busca' => 'search',
        ];

        foreach ($intents as $word => $intent) {
            if (str_contains($message, $word)) {
                return $intent;
            }
        }

        return null;
    }

    public function generateCodeFromIntent(string $intent, string $message): ?string
    {
        return match ($intent) {
            'math' => $this->generateMathCode($message),
            'loop' => "<?php\nfor (\$i = 1; \$i <= 10; \$i++) {\n    echo \"Número: \$i\\n\";\n}",
            'array' => "<?php\n\$frutas = ['Maçã', 'Banana', 'Laranja', 'Uva', 'Manga'];\nforeach (\$frutas as \$fruta) {\n    echo \"Fruta: \$fruta\\n\";\n}\necho \"Total: \" . count(\$frutas) . \" frutas\\n\";",
            'multiplication' => $this->generateMultiplicationTable($message),
            'average' => "<?php\n\$notas = [7.5, 8.0, 6.5, 9.0, 7.0];\n\$media = array_sum(\$notas) / count(\$notas);\necho \"Notas: \" . implode(', ', \$notas) . \"\\n\";\necho \"Média: \" . number_format(\$media, 2) . \"\\n\";\necho \"Situação: \" . (\$media >= 7 ? 'Aprovado' : 'Recuperação') . \"\\n\";",
            'imc' => "<?php\n\$peso = 70;\n\$altura = 1.75;\n\$imc = \$peso / (\$altura * \$altura);\necho \"Peso: {\$peso}kg\\n\";\necho \"Altura: {\$altura}m\\n\";\necho \"IMC: \" . number_format(\$imc, 2) . \"\\n\";\nif (\$imc < 18.5) echo \"Classificação: Abaixo do peso\\n\";\nelseif (\$imc < 25) echo \"Classificação: Peso normal\\n\";\nelseif (\$imc < 30) echo \"Classificação: Sobrepeso\\n\";\nelse echo \"Classificação: Obesidade\\n\";",
            'factorial' => "<?php\nfunction fatorial(\$n) {\n    if (\$n <= 1) return 1;\n    return \$n * fatorial(\$n - 1);\n}\n\$numero = 6;\necho \"Fatorial de \$numero = \" . fatorial(\$numero) . \"\\n\";\nfor (\$i = 1; \$i <= 10; \$i++) {\n    echo \"Fatorial de \$i = \" . fatorial(\$i) . \"\\n\";\n}",
            'fibonacci' => "<?php\nfunction fibonacci(\$n) {\n    \$seq = [0, 1];\n    for (\$i = 2; \$i < \$n; \$i++) {\n        \$seq[\$i] = \$seq[\$i-1] + \$seq[\$i-2];\n    }\n    return array_slice(\$seq, 0, \$n);\n}\n\$n = 15;\necho \"Sequência de Fibonacci (\$n termos):\\n\";\necho implode(', ', fibonacci(\$n)) . \"\\n\";\necho \"Soma: \" . array_sum(fibonacci(\$n)) . \"\\n\";",
            'sort' => "<?php\n\$numeros = [42, 7, 15, 3, 88, 21, 9, 54, 12, 30];\necho \"Original: \" . implode(', ', \$numeros) . \"\\n\";\nsort(\$numeros);\necho \"Crescente: \" . implode(', ', \$numeros) . \"\\n\";\nrsort(\$numeros);\necho \"Decrescente: \" . implode(', ', \$numeros) . \"\\n\";",
            'search' => "<?php\n\$dados = ['Ana', 'Bruno', 'Carla', 'Daniel', 'Eduarda', 'Fábio'];\n\$busca = 'Carla';\n\$pos = array_search(\$busca, \$dados);\nif (\$pos !== false) {\n    echo \"'{\$busca}' encontrado na posição \$pos\\n\";\n} else {\n    echo \"'{\$busca}' não encontrado\\n\";\n}\necho \"\\nLista completa:\\n\";\nforeach (\$dados as \$i => \$nome) {\n    echo \"  [\$i] \$nome\\n\";\n}",
            'test' => "<?php\n\$numeros = [5, 10, 15, 20, 25];\necho \"Array: \" . implode(', ', \$numeros) . \"\\n\";\necho \"Soma: \" . array_sum(\$numeros) . \"\\n\";\necho \"Média: \" . (array_sum(\$numeros) / count(\$numeros)) . \"\\n\";\necho \"Maior: \" . max(\$numeros) . \"\\n\";\necho \"Menor: \" . min(\$numeros) . \"\\n\";",
            'execute' => "<?php\necho \"Executando comando solicitado...\\n\";\necho \"Data e hora atual: \" . date('d/m/Y H:i:s') . \"\\n\";\necho \"Memória disponível: \" . round(memory_get_usage(true) / 1024 / 1024, 2) . \" MB\\n\";\necho \"PHP version: \" . PHP_VERSION . \"\\n\";",
            default => null,
        };
    }

    private function generateMathCode(string $message): string
    {
        preg_match_all('/\d+\.?\d*/', $message, $matches);
        $numbers = $matches[0] ?? [];

        if (count($numbers) >= 2) {
            $a = $numbers[0];
            $b = $numbers[1];
            if (str_contains($message, '+') || str_contains($message, 'mais') || str_contains($message, 'soma')) {
                return "<?php\n\$a = {$a};\n\$b = {$b};\n\$resultado = \$a + \$b;\necho \"{\$a} + {\$b} = \$resultado\\n\";";
            }
            if (str_contains($message, '-') || str_contains($message, 'menos') || str_contains($message, 'subtra')) {
                return "<?php\n\$a = {$a};\n\$b = {$b};\n\$resultado = \$a - \$b;\necho \"{\$a} - {\$b} = \$resultado\\n\";";
            }
            if (str_contains($message, '*') || str_contains($message, 'vezes') || str_contains($message, 'multiplica')) {
                return "<?php\n\$a = {$a};\n\$b = {$b};\n\$resultado = \$a * \$b;\necho \"{\$a} × {\$b} = \$resultado\\n\";";
            }
            if (str_contains($message, '/') || str_contains($message, 'divid') || str_contains($message, 'divisão')) {
                return "<?php\n\$a = {$a};\n\$b = {$b};\n\$resultado = \$b != 0 ? \$a / \$b : 'Erro: divisão por zero';\necho \"{\$a} ÷ {\$b} = \$resultado\\n\";";
            }
            return "<?php\n\$a = {$a};\n\$b = {$b};\necho \"Soma: \" . (\$a + \$b) . \"\\n\";\necho \"Subtração: \" . (\$a - \$b) . \"\\n\";\necho \"Multiplicação: \" . (\$a * \$b) . \"\\n\";\necho \"Divisão: \" . (\$b != 0 ? \$a / \$b : 'N/A') . \"\\n\";";
        }

        if (count($numbers) == 1) {
            return "<?php\n\$n = {$numbers[0]};\necho \"Tabuada de \$n:\\n\";\nfor (\$i = 1; \$i <= 10; \$i++) {\n    echo \"\$n × \$i = \" . \$n * \$i . \"\\n\";\n}";
        }

        return "<?php\n\$numeros = [10, 20, 30, 40, 50];\necho \"Array: \" . implode(', ', \$numeros) . \"\\n\";\necho \"Soma: \" . array_sum(\$numeros) . \"\\n\";\necho \"Média: \" . (array_sum(\$numeros) / count(\$numeros)) . \"\\n\";";
    }

    private function generateMultiplicationTable(string $message): string
    {
        preg_match('/\d+/', $message, $matches);
        $n = $matches[0] ?? 5;
        return "<?php\n\$n = {$n};\necho \"=== Tabuada do \$n ===\\n\\n\";\nfor (\$i = 1; \$i <= 10; \$i++) {\n    \$r = \$n * \$i;\n    echo \"\$n × \$i = \$r\\n\";\n}";
    }

    public function executeCodeFromMessage(string $message): ?string
    {
        $code = null;
        $language = 'php';

        if ($this->hasExplicitCodeBlock($message)) {
            preg_match('/```(\w+)?\s*\n?(.*?)```/s', $message, $matches);
            $language = strtolower($matches[1] ?? 'php');
            $code = trim($matches[2] ?? '');
        } else {
            $intent = $this->detectIntent($message);
            if ($intent) {
                $code = $this->generateCodeFromIntent($intent, $message);
            }
        }

        if (!$code) return null;

        return $this->runProcess($code, $language);
    }

    private function runProcess(string $code, string $language): string
    {
        $tmpDir = sys_get_temp_dir();
        $tmpFile = $tmpDir . '/opencode_' . uniqid() . '.' . ($language === 'python' ? 'py' : ($language === 'js' ? 'js' : 'php'));

        file_put_contents($tmpFile, $code);

        if (filesize($tmpFile) === 0) {
            unlink($tmpFile);
            return "Erro: código vazio.";
        }

        $command = match ($language) {
            'python', 'py' => "python3 " . escapeshellarg($tmpFile) . " 2>&1",
            'javascript', 'js' => "node " . escapeshellarg($tmpFile) . " 2>&1",
            'bash', 'sh' => "bash " . escapeshellarg($tmpFile) . " 2>&1",
            default => "php " . escapeshellarg($tmpFile) . " 2>&1",
        };

        $output = shell_exec($command);

        $output = mb_convert_encoding($output ?? '', 'UTF-8', 'UTF-8');

        unlink($tmpFile);

        if ($output === null || trim($output) === '') {
            return "Código executado (sem saída).";
        }

        return "Saída da execução:\n" . trim($output);
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
