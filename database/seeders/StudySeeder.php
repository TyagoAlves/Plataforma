<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\StudyMaterial;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Slide;
use App\Models\Podcast;
use Illuminate\Database\Seeder;

class StudySeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user) return;

        $subject = Subject::create([
            'user_id' => $user->id,
            'name' => 'Introdução à Inteligência Artificial',
            'description' => 'Fundamentos de IA, machine learning, redes neurais e aplicações práticas.',
        ]);

        $material = StudyMaterial::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'title' => 'Conceitos Básicos de IA',
            'content' => 'A Inteligência Artificial (IA) é um ramo da ciência da computação que busca criar sistemas capazes de realizar tarefas que normalmente requerem inteligência humana. Isso inclui aprendizado, raciocínio, percepção e compreensão de linguagem natural. O Machine Learning é uma subárea da IA que permite que máquinas aprendam a partir de dados sem serem explicitamente programadas. Redes Neurais Artificiais são inspiradas no cérebro humano e formam a base do Deep Learning.',
            'status' => 'processed',
        ]);

        $quiz = Quiz::create([
            'user_id' => $user->id,
            'study_material_id' => $material->id,
            'title' => 'Quiz: Conceitos Básicos de IA',
            'total_questions' => 3,
            'completed' => false,
        ]);

        $questions = [
            ['O que é Inteligência Artificial?', ['Ramo da biologia', 'Ramo da computação que cria sistemas inteligentes', 'Um tipo de hardware', 'Uma linguagem de programação'], 'Ramo da computação que cria sistemas inteligentes'],
            ['O que é Machine Learning?', ['Subárea da IA para aprendizado a partir de dados', 'Um tipo de robô', 'Um banco de dados', 'Uma rede social'], 'Subárea da IA para aprendizado a partir de dados'],
            ['No que as Redes Neurais Artificiais são inspiradas?', ['No cérebro humano', 'No sistema solar', 'No DNA', 'No núcleo atômico'], 'No cérebro humano'],
        ];

        foreach ($questions as $i => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q[0],
                'options' => $q[1],
                'correct_answer' => $q[2],
                'order' => $i,
            ]);
        }

        Slide::create([
            'user_id' => $user->id,
            'study_material_id' => $material->id,
            'title' => 'Slides: Conceitos Básicos de IA',
            'slides' => [
                ['title' => 'O que é IA?', 'content' => 'Inteligência Artificial é a simulação de processos de inteligência humana por sistemas computacionais.'],
                ['title' => 'Machine Learning', 'content' => 'Subárea da IA que permite que máquinas aprendam padrões a partir de dados.'],
                ['title' => 'Redes Neurais', 'content' => 'Inspiradas no cérebro humano, fundamentais para Deep Learning.'],
                ['title' => 'Aplicações', 'content' => 'IA está presente em assistentes virtuais, carros autônomos, diagnósticos médicos e muito mais.'],
            ],
        ]);

        Podcast::create([
            'user_id' => $user->id,
            'study_material_id' => $material->id,
            'title' => 'Podcast: Introdução à IA',
            'script' => "[Anfitrião]: Vamos falar sobre Inteligência Artificial!\n\n[Convidado]: A IA está revolucionando o mundo. Desde assistentes virtuais até diagnósticos médicos, as aplicações são infinitas.\n\n[Anfitrião]: Como começar a estudar IA?\n\n[Convidado]: Recomendo começar pelos fundamentos de lógica, estatística e programação, depois explorar machine learning.",
            'duration_seconds' => 480,
        ]);
    }
}
