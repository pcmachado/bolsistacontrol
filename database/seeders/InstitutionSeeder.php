<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;
use Faker\Factory as Faker;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Universidade de Caxias do Sul',
                'cnpj' => '12.345.678/0001-99',
                'city' => 'Caxias do Sul',
                'state' => 'RS',
                'address' => 'Rua Francisco Getúlio Vargas, 1130',
                'cnpj' => '12.345.678/0001-99',
                'email' => 'contato.ucs@example.ucs.br',
                'website' => 'www.ucs.br',
                'acronym' => 'ucs',
                'contact_person' => 'Maria Silva',
                'contact_email' => 'maria.silva@example.ucs.br',
                'contact_phone' => '(54) 3218-2100',
                'phone' => '(54) 3218-2100',
                'created_at' => now(),
            ],
            [
                'name' => 'Instituto Federal do Rio Grande do Sul',
                'cnpj' => '98.765.432/0001-00',
                'city' => 'Bento Gonçalves',
                'state' => 'RS',
                'address' => 'Av. Osvaldo Aranha, 540',
                'cnpj' => '98.765.432/0001-00',
                'email' => 'contato.ifrs@example.ifrs.br',
                'website' => 'www.ifrs.edu.br',
                'acronym' => 'ifrs',
                'contact_person' => 'João Pereira',
                'contact_email' => 'joao.pereira@example.ifrs.br',
                'contact_phone' => '(54) 3452-1260',
                'phone' => '(54) 3452-1260',
                'created_at' => now(),
            ],
        ];

        foreach ($institutions as $i) {
            Institution::create($i);
        }
    }
}
