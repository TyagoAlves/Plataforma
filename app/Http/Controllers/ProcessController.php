<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index(Request $request)
    {
        $query = Process::with('category');

        if ($request->filled('category_id')) {
            $query->where('process_category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%")
                  ->orWhere('number', 'like', "%{$request->keyword}%")
                  ->orWhere('content', 'like', "%{$request->keyword}%");
            });
        }

        $processes = $query->paginate(15);
        $categories = \App\Models\ProcessCategory::all();
        $types = Process::select('type')->distinct()->pluck('type');

        return view('processes.index', compact('processes', 'categories', 'types'));
    }

    public function show(Process $process)
    {
        $process->load('category');

        $process->response()->firstOrCreate(
            ['process_id' => $process->id],
            [
                'ai_suggestion' => $this->mockAiSuggestion($process->content),
                'status' => 'suggested',
            ]
        );

        $process->load('response');

        return view('processes.show', compact('process'));
    }

    public function submitResponse(Request $request, Process $process)
    {
        $request->validate(['final_response' => 'required|string']);

        $process->response()->updateOrCreate(
            ['process_id' => $process->id],
            ['final_response' => $request->final_response, 'status' => 'submitted']
        );

        return redirect()->route('processes.show', $process)
            ->with('success', 'Resposta enviada com sucesso!');
    }

    private function mockAiSuggestion(string $content): string
    {
        $suggestions = [
            "Com base na análise do processo, recomendamos o encaminhamento para o setor responsável com prioridade média. Sugerimos a elaboração de um parecer técnico detalhado sobre o assunto, considerando a legislação vigente e os precedentes aplicáveis ao caso.",
            "Após análise do conteúdo do processo, identificamos que a documentação apresentada está parcialmente completa. Recomenda-se solicitar os seguintes documentos complementares: comprovante de endereço atualizado, certidão negativa de débitos e procuração com poderes específicos.",
            "O processo em questão trata de uma solicitação que se enquadra nos critérios de análise simplificada. Sugerimos o deferimento do pedido, desde que verificadas as condições estabelecidas na portaria vigente. O prazo estimado para conclusão é de 15 dias úteis.",
            "Verificamos que o processo contém inconsistências nos dados informados. Recomenda-se a devolução para ajustes, com a indicação precisa dos campos que necessitam correção. Após o retorno, o processo deverá passar por nova triagem.",
        ];

        $index = crc32($content) % count($suggestions);
        return $suggestions[abs($index)];
    }
}
