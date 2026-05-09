<?php

namespace App\Services;

use App\Models\StudentDisciplineMonthRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AcademicRiskService
{
    // Thresholds de risco
    const CRITICAL_THRESHOLD = 25;

    const RISK_THRESHOLD = 15;

    const WARNING_THRESHOLD = 10;

    const CHURN_THRESHOLD = 40;  // Evasão provável

    /**
     * Analisa risco para uma turma específica
     */
    public function analyze($offeringId): Collection
    {
        $records = StudentDisciplineMonthRecord::where('class_offering_id', $offeringId)
            ->with('student', 'student.user')
            ->get()
            ->groupBy('student_id');

        $result = [];

        foreach ($records as $studentId => $months) {
            $first = $months->first();
            $totalClasses = $months->sum('total_classes');
            $absences = $months->sum('absences');

            $percent = $totalClasses > 0
                ? ($absences / $totalClasses) * 100
                : 0;

            $level = $this->getLevelByPercent($percent);
            $trend = $this->calculateTrend($months);

            $result[] = [
                'student_id' => $studentId,
                'student_name' => $first->student?->user?->name ?? "Aluno {$studentId}",
                'class_name' => $first->classOffering?->name ?? '-',
                'percent' => round($percent, 1),
                'level' => $level,
                'absences' => $absences,
                'total' => $totalClasses,
                'trend' => $trend,
                'is_churn_risk' => $percent >= self::CHURN_THRESHOLD,
            ];
        }

        return collect($result)->sortByDesc('percent');
    }

    /**
     * Analisa todos os alunos com dados de resumo
     */
    public function analyzeAll(): Collection
    {
        return StudentDisciplineMonthRecord::all()
            ->groupBy('student_id')
            ->map(function ($months) {
                $first = $months->first();
                $total = $months->sum('total_classes');
                $abs = $months->sum('absences');
                $percent = $total > 0 ? ($abs / $total) * 100 : 0;

                return [
                    'student_id' => $first->student_id,
                    'student_name' => $first->student?->user?->name ?? "Aluno {$first->student_id}",
                    'percent' => round($percent, 1),
                    'level' => $this->getLevelByPercent($percent),
                    'trend' => $this->calculateTrend($months),
                    'is_churn_risk' => $percent >= self::CHURN_THRESHOLD,
                ];
            });
    }

    /**
     * Retorna ranking de turmas críticas
     */
    public function getCriticalClassesRanking(): Collection
    {
        $offerings = \App\Models\ClassOffering::with('studentRecords')
            ->get()
            ->map(function ($offering) {
                $criticalCount = $offering->studentRecords
                    ->filter(fn ($r) => $this->getLevelByPercent(
                        $r->absences / max($r->total_classes, 1) * 100
                    ) === 'critical')
                    ->count();

                $totalStudents = $offering->studentRecords->count();
                $criticalPercent = $totalStudents > 0
                    ? ($criticalCount / $totalStudents) * 100
                    : 0;

                return [
                    'offering_id' => $offering->id,
                    'name' => $offering->name,
                    'course' => $offering->course?->name ?? '-',
                    'critical_count' => $criticalCount,
                    'total_students' => $totalStudents,
                    'critical_percent' => round($criticalPercent, 1),
                    'risk_level' => match (true) {
                        $criticalPercent > 40 => 'critical',
                        $criticalPercent > 20 => 'risk',
                        $criticalPercent > 10 => 'warning',
                        default => 'ok'
                    },
                ];
            })
            ->sortByDesc('critical_percent');

        return $offerings;
    }

    /**
     * Detecta potencial evasão baseado em padrões de comportamento
     */
    public function detectChurnRisk($studentId): array
    {
        $records = StudentDisciplineMonthRecord::where('student_id', $studentId)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        if ($records->count() < 2) {
            return ['risk' => false, 'reason' => 'Dados insuficientes'];
        }

        // Calcula % de faltas
        $total = $records->sum('total_classes');
        $absences = $records->sum('absences');
        $absencePercent = $total > 0 ? ($absences / $total) * 100 : 0;

        // Análise de tendência
        $lastMonth = $records->last();
        $previousMonth = $records->count() > 1 ? $records->get($records->count() - 2) : null;

        $isIncreasing = $previousMonth &&
            (($lastMonth->absences / max($lastMonth->total_classes, 1)) >
             ($previousMonth->absences / max($previousMonth->total_classes, 1)));

        // Lógica de risco de evasão
        $risks = [];

        if ($absencePercent >= self::CHURN_THRESHOLD) {
            $risks[] = "Faltas acima de {$this->CHURN_THRESHOLD}% ({$absencePercent}%)";
        }

        if ($isIncreasing) {
            $risks[] = 'Tendência crescente de faltas';
        }

        // Última presença antiga
        $lastMonth = Carbon::parse($lastMonth->year.'-'.$lastMonth->month.'-01');
        $monthsSinceLastClass = $lastMonth->diffInMonths(now());
        if ($monthsSinceLastClass > 1) {
            $risks[] = "Sem registros há {$monthsSinceLastClass} mês(es)";
        }

        return [
            'risk' => count($risks) > 0,
            'severity' => count($risks) >= 2 ? 'high' : 'medium',
            'reasons' => $risks,
            'absence_percent' => round($absencePercent, 1),
        ];
    }

    /**
     * Calcula tendência de faltas (últimas 4 semanas)
     */
    private function calculateTrend(Collection $records): string
    {
        if ($records->count() < 2) {
            return 'stable';
        }

        $sorted = $records->sortBy(fn ($r) => $r->year.'-'.str_pad($r->month, 2, '0', STR_PAD_LEFT));
        $recent = $sorted->slice(-2);

        if ($recent->count() < 2) {
            return 'stable';
        }

        $prev = $recent->first();
        $curr = $recent->last();

        $prevRate = $prev->total_classes > 0 ? $prev->absences / $prev->total_classes : 0;
        $currRate = $curr->total_classes > 0 ? $curr->absences / $curr->total_classes : 0;

        if ($currRate > $prevRate + 0.05) {
            return 'increasing';
        } elseif ($currRate < $prevRate - 0.05) {
            return 'decreasing';
        }

        return 'stable';
    }

    /**
     * Determina nível de risco baseado no percentual
     */
    private function getLevelByPercent(float $percent): string
    {
        return match (true) {
            $percent > self::CRITICAL_THRESHOLD => 'critical',
            $percent > self::RISK_THRESHOLD => 'risk',
            $percent > self::WARNING_THRESHOLD => 'warning',
            default => 'ok'
        };
    }
}
