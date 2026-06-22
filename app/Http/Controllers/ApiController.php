<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['receiveProcess']);
    }

    public function receiveProcess(Request $request)
    {
        $validated = $request->validate([
            'category_slug' => 'required|string|exists:process_categories,slug',
            'number' => ['required', 'string', Rule::unique('processes', 'number')],
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => ['required', 'string', Rule::in(['requerimento', 'solicitacao', 'recurso', 'parecer', 'oficio'])],
            'date' => 'required|date',
        ]);

        $category = \App\Models\ProcessCategory::where('slug', $validated['category_slug'])->first();

        $process = Process::create([
            'process_category_id' => $category->id,
            'number' => $validated['number'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'date' => $validated['date'],
        ]);

        return response()->json([
            'message' => 'Processo recebido com sucesso',
            'process' => $process
        ], 201);
    }

    public function getProcess(Process $process)
    {
        return response()->json($process->load('category', 'response'));
    }

    public function submitResponse(Request $request, Process $process)
    {
        $validated = $request->validate(['final_response' => 'required|string']);

        $response = $process->response()->updateOrCreate(
            ['process_id' => $process->id],
            ['final_response' => $validated['final_response'], 'status' => 'submitted']
        );

        return response()->json([
            'message' => 'Resposta enviada com sucesso',
            'response' => $response
        ]);
    }
}
