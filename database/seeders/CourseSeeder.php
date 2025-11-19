<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        //Course::factory()->count(8)->create();

        Course::insert([
            ['name' => 'Engenharia de Software', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Análise e Desenvolvimento de Sistemas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gestão Comercial', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administração', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ciência da Computação', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sistemas de Informação', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Recursos Humanos', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
