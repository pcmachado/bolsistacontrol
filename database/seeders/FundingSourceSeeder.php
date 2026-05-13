<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'FAURGS',
                'code' => 'FAURGS',
                'type' => 'external',
                'description' => 'Fundação de apoio para financiamento de projetos acadêmicos.',
                'total_amount' => 100000,
            ],
            [
                'name' => 'IFRS',
                'code' => 'IFRS',
                'type' => 'internal',
                'description' => 'Recursos institucionais do IFRS.',
                'total_amount' => 100000,
            ],
        ];

        foreach ($sources as $source) {
            FundingSource::updateOrCreate(
                ['name' => $source['name']],
                $source + ['active' => true]
            );
        }
    }
}
