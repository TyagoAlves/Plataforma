<?php

namespace Database\Seeders;

use App\Models\Process;
use App\Models\ProcessCategory;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ProcessCategory::all();
        $types = ['requerimento', 'solicitacao', 'recurso', 'parecer', 'oficio'];

        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                Process::create([
                    'process_category_id' => $category->id,
                    'number' => strtoupper(substr($category->slug, 0, 3)) . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT) . '/' . now()->year,
                    'title' => "Processo {$category->name} #{$i}",
                    'content' => "Este é o conteúdo detalhado do processo {$category->name} número {$i}. " .
                        "Descrevemos aqui todas as informações relevantes para análise e tramitação deste processo. " .
                        "Data de autuação: " . now()->subDays(rand(1, 365))->format('d/m/Y') . ". " .
                        "Interessado: Órgão solicitante. Assunto: " . $types[array_rand($types)] . " relacionado a {$category->name}.",
                    'status' => ['pending', 'in_analysis', 'completed'][array_rand(['pending', 'in_analysis', 'completed'])],
                    'date' => now()->subDays(rand(1, 365)),
                    'type' => $types[array_rand($types)],
                ]);
            }
        }
    }
}
