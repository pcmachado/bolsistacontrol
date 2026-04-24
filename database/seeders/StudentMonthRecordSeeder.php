<?php

namespace Database\Seeders;

use App\Models\StudentMonthRecord;
use App\Models\ClassOffering;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class StudentMonthRecordSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = ClassOffering::with('students')->get();

        $data = [];

        foreach ($offerings as $offering) {

            // ⚠️ evita erro se datas null
            if (!$offering->start_date || !$offering->end_date) {
                continue;
            }

            $period = CarbonPeriod::create(
                $offering->start_date,
                '1 month',
                $offering->end_date
            );

            foreach ($period as $date) {

                foreach ($offering->students as $student) {

                    $data[] = [
                        'student_id' => $student->id,
                        'class_offering_id' => $offering->id,
                        'month' => $date->month,
                        'year' => $date->year,
                        'absences' => rand(0, 5),
                        'attended_classes' => rand(10, 20),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // 🔥 insere em lote
        foreach (array_chunk($data, 500) as $chunk) {
            StudentMonthRecord::insert($chunk);
        }
    }
}