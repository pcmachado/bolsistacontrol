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
        ScholarshipHolder::factory()
            ->count(10)
            ->create()
            ->each(function ($holder) {
                AttendanceRecord::factory()
                    ->count(rand(5, 15))
                    ->create([
                        'scholarship_holder_id' => $holder->id,
                    ]);
            });
    }
}
