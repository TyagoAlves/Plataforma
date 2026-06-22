<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('user_id', auth()->id())
            ->withCount('questions')
            ->latest()
            ->paginate(10);
        return view('study.quizzes.index', compact('quizzes'));
    }

    public function create(Request $request)
    {
        $materialId = $request->get('material_id');
        $material = $materialId ? StudyMaterial::findOrFail($materialId) : null;
        return view('study.quizzes.create', compact('material'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'study_material_id' => 'nullable|exists:study_materials,id',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correct_answer' => 'required|string',
        ]);

        $quiz = Quiz::create([
            'user_id' => auth()->id(),
            'study_material_id' => $validated['study_material_id'] ?? null,
            'title' => $validated['title'],
            'total_questions' => count($validated['questions']),
        ]);

        foreach ($validated['questions'] as $order => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'options' => $q['options'],
                'correct_answer' => $q['correct_answer'],
                'order' => $order,
            ]);
        }

        return redirect()->route('study.quizzes.show', $quiz)
            ->with('success', 'Simulado criado!');
    }

    public function show(Quiz $quiz)
    {
        $this->authorizeAccess($quiz);
        $quiz->load('questions');
        return view('study.quizzes.show', compact('quiz'));
    }

    public function answer(Request $request, Quiz $quiz)
    {
        $this->authorizeAccess($quiz);

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        $correct = 0;
        foreach ($validated['answers'] as $questionId => $answer) {
            $question = QuizQuestion::findOrFail($questionId);
            $isCorrect = $question->correct_answer === $answer;
            $question->update([
                'user_answer' => $answer,
                'answered_correctly' => $isCorrect,
            ]);
            if ($isCorrect) $correct++;
        }

        $quiz->update([
            'correct_answers' => $correct,
            'completed' => true,
        ]);

        return redirect()->route('study.quizzes.results', $quiz);
    }

    public function results(Quiz $quiz)
    {
        $this->authorizeAccess($quiz);
        $quiz->load('questions');
        return view('study.quizzes.results', compact('quiz'));
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorizeAccess($quiz);
        $quiz->questions()->delete();
        $quiz->delete();

        return redirect()->route('study.quizzes.index')
            ->with('success', 'Simulado removido.');
    }

    public function generateFromMaterial(Request $request)
    {
        $validated = $request->validate([
            'study_material_id' => 'required|exists:study_materials,id',
        ]);

        $material = StudyMaterial::findOrFail($validated['study_material_id']);

        $quiz = Quiz::create([
            'user_id' => auth()->id(),
            'study_material_id' => $material->id,
            'title' => "Simulado: {$material->title}",
            'total_questions' => 5,
        ]);

        $questions = $this->mockGenerateQuestions($material->content ?? $material->title);
        foreach ($questions as $order => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'options' => $q['options'],
                'correct_answer' => $q['correct_answer'],
                'order' => $order,
            ]);
        }

        return redirect()->route('study.quizzes.show', $quiz)
            ->with('success', 'Simulado gerado por IA!');
    }

    private function mockGenerateQuestions(string $content): array
    {
        return [
            [
                'question' => "Qual o principal conceito abordado no material '{$content}'?",
                'options' => ['Conceito A', 'Conceito B', 'Conceito C', 'Conceito D'],
                'correct_answer' => 'Conceito A',
            ],
            [
                'question' => 'Qual alternativa melhor descreve a aplicação prática desse conteúdo?',
                'options' => ['Prática 1', 'Prática 2', 'Prática 3', 'Prática 4'],
                'correct_answer' => 'Prática 2',
            ],
            [
                'question' => 'O que a literatura recomenda sobre este tópico?',
                'options' => ['Recomendação X', 'Recomendação Y', 'Recomendação Z', 'Todas as acima'],
                'correct_answer' => 'Recomendação X',
            ],
            [
                'question' => 'Qual a importância deste assunto para a área de estudo?',
                'options' => ['Importância alta', 'Importância média', 'Importância baixa', 'Nenhuma importância'],
                'correct_answer' => 'Importância alta',
            ],
            [
                'question' => 'Como este conhecimento se relaciona com outras disciplinas?',
                'options' => ['Relação direta', 'Relação indireta', 'Sem relação', 'Relação multidisciplinar'],
                'correct_answer' => 'Relação multidisciplinar',
            ],
        ];
    }

    private function authorizeAccess(Quiz $quiz): void
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
