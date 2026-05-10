<?php

namespace Database\Seeders;

use App\Models\FinancialClosure;
use App\Models\Unit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class FinancialClosureSeeder extends Seeder
{
    public function run(): void
    {
        $closedBy = User::role('admin')->first() ?? User::role('superadmin')->first();

        if (! $closedBy) {
            $this->command?->warn('FinancialClosureSeeder: usuário responsável ausente.');

            return;
        }

        $period = CarbonPeriod::create(now()->copy()->startOfYear(), '1 month', now()->copy()->subMonthNoOverflow()->startOfMonth());

        foreach (Unit::all() as $unit) {
            foreach ($period as $date) {
                FinancialClosure::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'month' => $date->month,
                        'year' => $date->year,
                    ],
                    [
                        'closed_at' => $date->copy()->endOfMonth()->addDays(6),
                        'closed_by_user_id' => $closedBy->id,
                    ]
                );
            }
        }
    }
}
