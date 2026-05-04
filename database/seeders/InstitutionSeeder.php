<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Universidade de Caxias do Sul',
                'acronym' => 'ucs',
                'cnpj' => '12.345.678/0001-99',
                'city' => 'Caxias do Sul',
                'state' => 'RS',
                'address' => 'Rua Francisco Getulio Vargas, 1130',
                'email' => 'contato@ucs.example.br',
                'website' => 'https://www.ucs.br',
                'contact_person' => 'Maria Silva',
                'contact_email' => 'maria.silva@ucs.example.br',
                'contact_phone' => '(54) 3218-2100',
                'phone' => '(54) 3218-2100',
                'country' => 'Brasil',
            ],
            [
                'name' => 'Instituto Federal do Rio Grande do Sul',
                'acronym' => 'ifrs',
                'cnpj' => '98.765.432/0001-00',
                'city' => 'Bento Goncalves',
                'state' => 'RS',
                'address' => 'Avenida Osvaldo Aranha, 540',
                'email' => 'contato@ifrs.example.br',
                'website' => 'https://www.ifrs.edu.br',
                'contact_person' => 'Joao Pereira',
                'contact_email' => 'joao.pereira@ifrs.example.br',
                'contact_phone' => '(54) 3452-1260',
                'phone' => '(54) 3452-1260',
                'country' => 'Brasil',
            ],
        ];

        foreach ($institutions as $data) {
            Institution::updateOrCreate(
                ['cnpj' => $data['cnpj']],
                $data
            );
        }
    }
}
