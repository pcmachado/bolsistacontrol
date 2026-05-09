<?php

namespace App\Services;

use App\Models\ClassSession;

class ClassLoadService
{
    public function getMonthlyLoad(
        int $classOfferingId,
        int $disciplineId,
        int $month,
        int $year
    ): float {

        return ClassSession::query()
            ->where('class_offering_id', $classOfferingId)
            ->where('discipline_id', $disciplineId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('duration_hours'); // 🔥 soma horas reais
    }
}
