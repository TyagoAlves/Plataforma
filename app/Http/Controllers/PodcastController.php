<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function index()
    {
        $podcasts = Podcast::where('user_id', auth()->id())->latest()->paginate(10);
        return view('study.podcasts.index', compact('podcasts'));
    }

    public function show(Podcast $podcast)
    {
        $this->authorizeAccess($podcast);
        $podcast->load('studyMaterial');
        return view('study.podcasts.show', compact('podcast'));
    }

    public function generateFromMaterial(Request $request)
    {
        $validated = $request->validate([
            'study_material_id' => 'required|exists:study_materials,id',
        ]);

        $material = StudyMaterial::findOrFail($validated['study_material_id']);

        $script = $this->mockGenerateScript($material->content ?? $material->title);

        $podcast = Podcast::create([
            'user_id' => auth()->id(),
            'study_material_id' => $material->id,
            'title' => "Podcast: {$material->title}",
            'script' => $script,
            'duration_seconds' => rand(300, 900),
        ]);

        return redirect()->route('study.podcasts.show', $podcast)
            ->with('success', 'Podcast gerado por IA!');
    }

    private function mockGenerateScript(string $content): string
    {
        return <<<SCRIPT
[Anfitrião]: Bem-vindos ao nosso podcast de estudos! Hoje vamos debater sobre: {$content}

[Convidado]: Excelente tema! Este é um assunto fundamental para quem está se preparando para provas e quer realmente entender a matéria.

[Anfitrião]: Vamos começar com os conceitos básicos. O que você destacaria como principal fundamento?

[Convidado]: O ponto de partida é entender que estamos falando de um conteúdo que exige tanto memorização quanto compreensão prática. Recomendo sempre começar pelos fundamentos teóricos.

[Anfitrião]: E como aplicar isso na prática?

[Convidado]: A melhor forma é através de exercícios e simulados. Ao testar seu conhecimento, você identifica lacunas e pode focar seus estudos onde realmente precisa.

[Anfitrião]: Excelente dica! E para quem tem pouco tempo de estudo?

[Convidado]: Foque nos tópicos mais cobrados e faça resumos visuais. Slides e mapas mentais ajudam muito na fixação do conteúdo.

[Anfitrião]: Ótimo! Vamos recapitular os pontos principais:
1. Entenda os fundamentos teóricos
2. Pratique com exercícios e simulados
3. Use resumos visuais para fixação
4. Identifique e preencha lacunas de conhecimento

[Convidado]: Perfeito! Estudem com consistência e os resultados virão.

[Anfitrião]: Obrigado por participar! Nos vemos no próximo episódio.
SCRIPT;
    }

    private function authorizeAccess(Podcast $podcast): void
    {
        if ($podcast->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
