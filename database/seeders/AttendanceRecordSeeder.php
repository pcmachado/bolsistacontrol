<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $scholarshipHolders = ScholarshipHolder::all();
        $coordinator = User::role('coordenador_adjunto')->first();

        foreach ($scholarshipHolders as $holder) {
            // Cria registros em diferentes estados
            AttendanceRecord::factory()->count(3)->create([
                'scholarship_holder_id' => $holder->id,
                'status' => AttendanceRecord::STATUS_DRAFT,
            ]);

            AttendanceRecord::factory()->count(2)->create([
                'scholarship_holder_id' => $holder->id,
                'status' => AttendanceRecord::STATUS_PENDING,
                'submitted_at' => Carbon::now()->subDays(2),
            ]);

            AttendanceRecord::factory()->count(2)->create([
                'scholarship_holder_id' => $holder->id,
                'status' => AttendanceRecord::STATUS_APPROVED,
                'submitted_at' => Carbon::now()->subDays(5),
                'approved_by_user_id' => $coordinator->id,
            ]);

            AttendanceRecord::factory()->count(1)->create([
                'scholarship_holder_id' => $holder->id,
                'status' => AttendanceRecord::STATUS_REJECTED,
                'submitted_at' => Carbon::now()->subDays(3),
                'approved_by_user_id' => $coordinator->id,
                'rejection_reason' => 'Horário inconsistente com jornada semanal.',
            ]);
        }
    }
}
