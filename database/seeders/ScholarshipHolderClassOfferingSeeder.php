<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScholarshipHolderClassOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $holders = ScholarshipHolder::all();
        $classes = ClassOffering::all();

        // Verifica se existem dados nas tabelas principais antes de prosseguir
        if ($holders->isEmpty() || $classes->isEmpty()) {
            $this->command?->warn('É necessário ter bolsistas e turmas cadastrados antes de rodar este seeder.');
            return;
        }

        $roles = ['Orientador', 'Professor', 'Apoio'];

        // Para cada turma, vamos vincular de 1 a 2 bolsistas aleatórios
        foreach ($classes as $class) {
            
            // Sorteia 1 ou 2 bolsistas
            $randomHolders = $holders->random(rand(1, 2));

            foreach ($randomHolders as $holder) {
                // Insere diretamente na tabela pivot
                DB::table('scholarship_holder_class_offering')->updateOrInsert(
                    [
                        'scholarship_holder_id' => $holder->id,
                        'class_offering_id' => $class->id,
                    ],
                    [
                        'role' => $roles[array_rand($roles)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}