<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Position;
use App\Models\ProjectScholarshipHolder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProjectScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        /*$project = Project::first();
        $holders = ScholarshipHolder::take(3)->get();
        $positions = Position::take(3)->get();

        foreach ($holders as $i => $holder) {
            ProjectScholarshipHolder::create([
                'project_id' => $project->id,
                'scholarship_holder_id' => $holder->id,
                'position_id' => $positions[$i % $positions->count()]->id,
                'weekly_workload' => rand(10, 20),
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => null,
                'status' => 'active',
            ]);
        }*/

        $faker = Faker::create('pt_BR');

        // Busca todos os bolsistas
        $scholarshipHolders = ScholarshipHolder::with('unit')->get();

        if ($scholarshipHolders->isEmpty()) {
            echo "⚠ Nenhum bolsista encontrado — ProjectScholarshipHolderSeeder ignorado.\n";
            return;
        }

        echo "🔗 Vinculando bolsistas aos projetos...\n";

        foreach ($scholarshipHolders as $holder) {

            // Obtém projetos da mesma instituição
            $projects = Project::where('institution_id', $holder->unit->institution_id)->get();

            if ($projects->isEmpty()) {
                echo "⚠ Nenhum projeto encontrado para Instituição ID {$holder->unit->institution_id}\n";
                continue;
            }

            // Escolhe 1 projeto aleatório
            $project = $projects->random();

            ProjectScholarshipHolder::firstOrCreate(
                [
                    'project_id'            => $project->id,
                    'scholarship_holder_id' => $holder->id,
                ],
                [
                    'position_id'      => rand(1, 3), // Ajuste para cargos reais
                    'weekly_workload'  => [10, 20, 30][array_rand([10,20,30])],
                    'start_date'       => $faker->dateTimeBetween('-8 months', '-2 months'),
                    'end_date'         => $faker->dateTimeBetween('now', '+6 months'),
                    'assignments'      => $faker->sentence(10),
                    'status'           => 'active',
                ]
            );

            echo "   ✔ Bolsista {$holder->name} vinculado ao Projeto {$project->shortname}\n";
        }

        echo "🎉 Finalizado: ProjectScholarshipHolderSeeder.\n";
    }
}
