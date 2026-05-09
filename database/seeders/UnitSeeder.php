<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Unit;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        $map = [
            'ucs' => [
                ['name' => 'UCS - Tecnologia', 'shortname' => 'ucs-tec', 'domain' => 'tecnologia.ucs.br'],
                ['name' => 'UCS - Bento Goncalves', 'shortname' => 'ucs-bento', 'domain' => 'bento.ucs.br'],
                ['name' => 'UCS - Caxias do Sul', 'shortname' => 'ucs-caxias', 'domain' => 'ucs.br'],
                ['name' => 'UCS - Reitoria', 'shortname' => 'ucs-reitoria', 'domain' => 'reitoria.ucs.br', 'is_administrative' => true],
            ],
            'ifrs' => [
                ['name' => 'IFRS - Campus Bento Goncalves', 'shortname' => 'ifrs-bento', 'domain' => 'bento.ifrs.edu.br'],
                ['name' => 'IFRS - Campus Farroupilha', 'shortname' => 'ifrs-farroupilha', 'domain' => 'farroupilha.ifrs.edu.br'],
                ['name' => 'IFRS - Reitoria', 'shortname' => 'ifrs-reitoria', 'domain' => 'ifrs.edu.br', 'is_administrative' => true],
            ],
        ];

        foreach ($map as $acronym => $units) {
            $institution = Institution::where('acronym', $acronym)->first();

            if (! $institution) {
                $this->command?->warn("UnitSeeder: instituicao {$acronym} nao encontrada.");

                continue;
            }

            foreach ($units as $data) {
                Unit::updateOrCreate(
                    [
                        'institution_id' => $institution->id,
                        'shortname' => $data['shortname'],
                    ],
                    [
                        'name' => $data['name'],
                        'domain' => $data['domain'],
                        'city' => $institution->city,
                        'address' => $faker->address(),
                        'phone' => $faker->phoneNumber(),
                        'email' => $faker->unique()->companyEmail(),
                        'cnpj' => $faker->cnpj(),
                        'is_administrative' => $data['is_administrative'] ?? false,
                    ]
                );
            }
        }
    }
}
