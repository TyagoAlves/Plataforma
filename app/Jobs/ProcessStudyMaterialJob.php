<?php

namespace App\Jobs;

use App\Models\StudyMaterial;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Slide;
use App\Models\Podcast;
use App\Services\GeminiService;
use App\Services\TtsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessStudyMaterialJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public StudyMaterial $material
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $content = $this->material->content;
        if (empty($content)) {
            $this->material->update(['status' => 'error']);
            Log::error('Material processing failed: empty content', ['material_id' => $this->material->id]);
            return;
        }

        $this->material->update(['status' => 'processing']);

        $quizGenerated = false;
        $slidesGenerated = false;
        $podcastGenerated = false;

        try {
            if ($gemini->isAvailable()) {
                $this->processWithGemini($gemini, $content, $quizGenerated, $slidesGenerated, $podcastGenerated);
            } else {
                $this->generateMockContent($content);
                $quizGenerated = true;
                $slidesGenerated = true;
                $podcastGenerated = true;
            }

            $this->material->update(['status' => 'processed']);
        } catch (\Exception $e) {
            $this->material->update(['status' => 'error']);
            Log::error('Material processing exception', [
                'material_id' => $this->material->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        Log::info('Material processed', [
            'material_id' => $this->material->id,
            'quiz' => $quizGenerated,
            'slides' => $slidesGenerated,
            'podcast' => $podcastGenerated,
        ]);
    }

    private function processWithGemini(GeminiService $gemini, string $content, bool &$quizGenerated, bool &$slidesGenerated, bool &$podcastGenerated): void
    {
        $questions = $gemini->generateQuiz($content);
        if ($questions) {
            $quiz = Quiz::create([
                'user_id' => $this->material->user_id,
                'study_material_id' => $this->material->id,
                'title' => "Simulado: {$this->material->title}",
                'total_questions' => count($questions),
            ]);

            foreach ($questions as $i => $q) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $q['question'] ?? 'Questão',
                    'options' => $q['options'] ?? [],
                    'correct_answer' => $q['correct_answer'] ?? '',
                    'order' => $i,
                ]);
            }
            $quizGenerated = true;
        }

        $slidesData = $gemini->generateSlides($content);
        if ($slidesData) {
            Slide::create([
                'user_id' => $this->material->user_id,
                'study_material_id' => $this->material->id,
                'title' => "Slides: {$this->material->title}",
                'slides' => $slidesData,
            ]);
            $slidesGenerated = true;
        }

        $script = $gemini->generatePodcastScript($content);
        if ($script) {
            $podcast = Podcast::create([
                'user_id' => $this->material->user_id,
                'study_material_id' => $this->material->id,
                'title' => "Podcast: {$this->material->title}",
                'script' => $script,
                'duration_seconds' => strlen($script) / 10,
            ]);
            $podcastGenerated = true;

            $this->generatePodcastAudio($podcast, $script);
        }
    }

    private function generatePodcastAudio(Podcast $podcast, string $script): void
    {
        try {
            $tts = app(TtsService::class);
            $speechText = $tts->scriptToSpeechText($script);
            $filename = 'podcast_' . $podcast->id . '_' . time();
            $audioPath = $tts->generateAudio($speechText, $filename);

            if ($audioPath) {
                $duration = $tts->estimateDuration($speechText);
                $podcast->update([
                    'audio_path' => $audioPath,
                    'duration_seconds' => $duration,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Podcast audio generation failed during job processing', [
                'podcast_id' => $podcast->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function generateMockContent(string $content): void
    {
        Quiz::create([
            'user_id' => $this->material->user_id,
            'study_material_id' => $this->material->id,
            'title' => "Simulado: {$this->material->title}",
            'total_questions' => 5,
        ]);

        $quiz = Quiz::where('study_material_id', $this->material->id)->latest()->first();
        if ($quiz) {
            $mockQuestions = [
                ['Questão 1: Qual o principal conceito?', ['Conceito A', 'Conceito B', 'Conceito C', 'Conceito D'], 'Conceito A'],
                ['Questão 2: Qual a aplicação prática?', ['Prática 1', 'Prática 2', 'Prática 3', 'Prática 4'], 'Prática 2'],
                ['Questão 3: O que a literatura recomenda?', ['Recomendação X', 'Recomendação Y', 'Recomendação Z'], 'Recomendação X'],
                ['Questão 4: Qual a importância?', ['Alta', 'Média', 'Baixa'], 'Alta'],
                ['Questão 5: Relação com outras áreas?', ['Direta', 'Indireta', 'Multidisciplinar'], 'Multidisciplinar'],
            ];
            foreach ($mockQuestions as $i => $mq) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $mq[0],
                    'options' => $mq[1],
                    'correct_answer' => $mq[2],
                    'order' => $i,
                ]);
            }
        }

        $slides = [];
        $sentences = explode('.', $content);
        $slides[] = ['title' => 'Introdução', 'content' => substr($content, 0, 150) ?: 'Conteúdo em análise'];
        $slides[] = ['title' => 'Conceitos Principais', 'content' => implode('. ', array_filter(array_slice($sentences, 0, 3)))];
        $slides[] = ['title' => 'Aplicações', 'content' => 'Aplicações práticas deste conteúdo no contexto acadêmico e profissional.'];
        $slides[] = ['title' => 'Resumo', 'content' => 'Principais pontos abordados e recomendações para aprofundamento.'];
        $slides[] = ['title' => 'Referências', 'content' => 'Baseado no conteúdo fornecido pelo usuário.'];

        Slide::create([
            'user_id' => $this->material->user_id,
            'study_material_id' => $this->material->id,
            'title' => "Slides: {$this->material->title}",
            'slides' => $slides,
        ]);

        $script = "[Anfitrião]: Bem-vindos ao podcast de estudos! Hoje vamos debater: {$this->material->title}\n\n"
            . "[Convidado]: Este é um tema fascinante! Vamos explorar os principais conceitos.\n\n"
            . "[Anfitrião]: Quais os pontos mais importantes?\n\n"
            . "[Convidado]: Primeiro, precisamos entender os fundamentos. Depois, aplicar na prática.\n\n"
            . "[Anfitrião]: Excelente resumo! Obrigado pela participação.";

        $podcast = Podcast::create([
            'user_id' => $this->material->user_id,
            'study_material_id' => $this->material->id,
            'title' => "Podcast: {$this->material->title}",
            'script' => $script,
            'duration_seconds' => strlen($script) / 10,
        ]);

        $this->generatePodcastAudio($podcast, $script);
    }
}
