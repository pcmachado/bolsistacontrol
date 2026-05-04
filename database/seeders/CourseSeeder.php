<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courseNames = [
            'Engenharia de Software',
            'Análise e Desenvolvimento de Sistemas',
            'Gestão Comercial',
            'Administração',
            'Ciência da Computação',
            'Sistemas de Informação',
            'Marketing',
            'Recursos Humanos',
        ];

        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            foreach ($courseNames as $name) {
                Course::updateOrCreate(
                    [
                        'name' => $name,
                        'institution_id' => $institution->id,
                    ],
                    [
                        'name' => $name,
                        'institution_id' => $institution->id,
                        'description' => "Curso de {$name} na instituição {$institution->name}",
                    ]
                );
            }
        }
    }
}
