<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentRecord;
use Carbon\Carbon;

class ClassOfferingSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = ClassOffering::all();

        $data = [];

        foreach ($offerings as $offering) {

            // ⚠️ proteção
            if (!$offering->start_date || !$offering->end_date) {
                continue;
            }

            if ($offering->end_date < $offering->start_date) {
                continue;
            }

            $period = \Carbon\CarbonPeriod::create(
                $offering->start_date,
                '1 month',
                $offering->end_date
            );

            foreach ($period as $date) {

                $data[] = [
                    'class_offering_id' => $offering->id,
                    'month' => $date->month,
                    'year' => $date->year,
                    'status' => collect(['draft', 'submitted', 'approved'])->random(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 🔥 insert em lote
        foreach (array_chunk($data, 500) as $chunk) {
            ClassOfferingSubmission::insert($chunk);
        }
    }
}