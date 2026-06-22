<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Jobs\ProcessStudyMaterialJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function create()
    {
        $subjects = Subject::where('user_id', auth()->id())->get();
        return view('study.materials.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,txt,epub|max:10240',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'subject_id' => $validated['subject_id'] ?? null,
            'status' => 'pending',
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('study-materials', 'public');
            $data['file_path'] = $path;
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
            $data['content'] = $this->extractTextFromFile($path, $data['file_type']);
        } elseif ($request->filled('content')) {
            $data['content'] = $validated['content'];
        }

        $material = StudyMaterial::create($data);

        ProcessStudyMaterialJob::dispatch($material);

        return redirect()->route('study.dashboard')
            ->with('success', 'Material adicionado! IA está processando o conteúdo em segundo plano...');
    }

    public function show(StudyMaterial $material)
    {
        $this->authorizeAccess($material);
        $material->load('subject');
        return view('study.materials.show', compact('material'));
    }

    public function destroy(StudyMaterial $material)
    {
        $this->authorizeAccess($material);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();

        return redirect()->route('study.dashboard')
            ->with('success', 'Material removido.');
    }

    private function extractTextFromFile(string $path, string $type): string
    {
        $fullPath = Storage::disk('public')->path($path);
        if ($type === 'txt') {
            return file_get_contents($fullPath);
        }
        return '';
    }

    private function authorizeAccess(StudyMaterial $material): void
    {
        if ($material->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
