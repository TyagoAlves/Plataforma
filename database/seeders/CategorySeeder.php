<?php

namespace Database\Seeders;

use App\Models\ProcessCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Administrativo', 'slug' => 'administrativo', 'description' => 'Processos administrativos internos', 'icon' => 'folder'],
            ['name' => 'Financeiro', 'slug' => 'financeiro', 'description' => 'Processos financeiros e contábeis', 'icon' => 'currency-dollar'],
            ['name' => 'Jurídico', 'slug' => 'juridico', 'description' => 'Processos jurídicos e legais', 'icon' => 'scale'],
            ['name' => 'RH', 'slug' => 'recursos-humanos', 'description' => 'Processos de recursos humanos', 'icon' => 'users'],
            ['name' => 'Compras', 'slug' => 'compras', 'description' => 'Processos de aquisição e licitação', 'icon' => 'shopping-cart'],
            ['name' => 'TI', 'slug' => 'tecnologia', 'description' => 'Processos de tecnologia da informação', 'icon' => 'cpu'],
        ];

        foreach ($categories as $cat) {
            ProcessCategory::create($cat);
        }
    }
}
