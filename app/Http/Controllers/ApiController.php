<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function receiveProcess(Request $request)
    {
        $validated = $request->validate([
            'category_slug' => 'required|string',
            'number' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => 'required|string',
            'date' => 'required|date',
        ]);

        $category = \App\Models\ProcessCategory::where('slug', $validated['category_slug'])->first();

        if (!$category) {
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }

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
