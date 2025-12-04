<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceRecordSeeder extends Seeder
{
    public function run()
    {
        // Cria 10 bolsistas com registros de frequência
        /*ScholarshipHolder::factory()
            ->count(10)
            ->create()
            ->each(function ($holder) {
                AttendanceRecord::factory()
                    ->count(rand(5, 15))
                    ->create([
                        'scholarship_holder_id' => $holder->id,
                    ]);
            });*/

        $months = ['2025-09', '2025-10', '2025-11'];
        $statuses = ['submitted', 'approved', 'rejected', 'draft', 'late'];

        $holders = ScholarshipHolder::all();

        foreach ($holders as $holder) {
            foreach ($months as $m) {
                // cria 4 registros por mês, dias 2, 8, 15, 22 (evita 31s)
                $days = [2, 8, 15, 22];
                foreach ($days as $d) {
                    $date = Carbon::createFromFormat('Y-m-d', "{$m}-" . str_pad($d, 2, '0', STR_PAD_LEFT));
                    AttendanceRecord::create([
                        'scholarship_holder_id' => $holder->id,
                        'date' => $date->toDateString(),
                        'start_time' => $date->copy()->setTime(9, 0)->toTimeString(),
                        'end_time' => $date->copy()->setTime(13, 0)->toTimeString(),
                        'hours' => 4,
                        'observation' => "Frequência gerada pelo seeder para {$date->format('d/m/Y')}",
                        'calculated_value' => null,
                        'approved' => false,
                        'status' => $statuses[array_rand($statuses)],
                        'submitted_at' => null,
                        'approved_by_user_id' => null,
                        'rejected_at' => null,
                        'rejected_reason' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
