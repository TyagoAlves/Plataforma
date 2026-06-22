<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\Subject;
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

        $userId = $material->user_id;

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        foreach ($material->quizzes as $quiz) {
            $quiz->questions()->delete();
            $quiz->delete();
        }
        foreach ($material->slides as $slide) {
            $slide->delete();
        }
        foreach ($material->podcasts as $podcast) {
            if ($podcast->audio_path) {
                Storage::disk('public')->delete($podcast->audio_path);
            }
            $podcast->delete();
        }

        $userStorageDir = storage_path("app/public/users/{$userId}");
        if (is_dir($userStorageDir)) {
            \Illuminate\Support\Facades\File::cleanDirectory($userStorageDir);
        }

        $material->delete();

        return redirect()->route('study.dashboard')
            ->with('success', 'Material e todos os itens vinculados removidos.');
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
