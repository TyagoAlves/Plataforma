<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::where('user_id', auth()->id())->latest()->paginate(10);
        return view('study.slides.index', compact('slides'));
    }

    public function show(Slide $slide)
    {
        $this->authorizeAccess($slide);
        $slide->load('studyMaterial');
        return view('study.slides.show', compact('slide'));
    }

    public function destroy(Slide $slide)
    {
        $this->authorizeAccess($slide);
        $slide->delete();

        return redirect()->route('study.slides.index')
            ->with('success', 'Slides removidos.');
    }

    public function generateFromMaterial(Request $request)
    {
        $validated = $request->validate([
            'study_material_id' => 'required|exists:study_materials,id',
        ]);

        $material = StudyMaterial::findOrFail($validated['study_material_id']);

        $slide = Slide::create([
            'user_id' => auth()->id(),
            'study_material_id' => $material->id,
            'title' => "Slides: {$material->title}",
            'slides' => $this->mockGenerateSlides($material->content ?? $material->title),
        ]);

        return redirect()->route('study.slides.show', $slide)
            ->with('success', 'Slides gerados por IA!');
    }

    private function mockGenerateSlides(string $content): array
    {
        $sentences = explode('.', $content);
        $slides = [];

        $slides[] = [
            'title' => 'Introdução',
            'content' => "Visão geral do tópico: " . (substr($content, 0, 100) ?: 'Conteúdo em análise'),
        ];

        $slides[] = [
            'title' => 'Conceitos Principais',
            'content' => implode("\n\n", array_filter(array_slice($sentences, 0, 3))),
        ];

        $slides[] = [
            'title' => 'Aplicações Práticas',
            'content' => 'Este conteúdo pode ser aplicado em diversos contextos acadêmicos e profissionais, permitindo uma compreensão mais profunda do tema abordado.',
        ];

        $slides[] = [
            'title' => 'Resumo',
            'content' => 'Os principais pontos abordados incluem fundamentos teóricos, exemplos práticos e recomendações para aprofundamento no tema.',
        ];

        $slides[] = [
            'title' => 'Referências',
            'content' => 'Material baseado no conteúdo fornecido pelo usuário. Consulte a bibliografia recomendada para estudo adicional.',
        ];

        return $slides;
    }

    private function authorizeAccess(Slide $slide): void
    {
        if ($slide->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
