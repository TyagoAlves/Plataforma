<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('user_id', auth()->id())->withCount('studyMaterials')->get();
        return view('study.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return redirect()->route('study.subjects.index')
            ->with('success', 'Matéria criada com sucesso!');
    }

    public function show(Subject $subject)
    {
        $this->authorizeAccess($subject);
        $subject->load('studyMaterials');
        return view('study.subjects.show', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $this->authorizeAccess($subject);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);

        return redirect()->route('study.subjects.index')
            ->with('success', 'Matéria atualizada!');
    }

    public function destroy(Subject $subject)
    {
        $this->authorizeAccess($subject);

        if ($subject->studyMaterials()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir: existem materiais vinculados a esta matéria.');
        }

        $subject->delete();

        return redirect()->route('study.subjects.index')
            ->with('success', 'Matéria removida.');
    }

    private function authorizeAccess(Subject $subject): void
    {
        if ($subject->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
