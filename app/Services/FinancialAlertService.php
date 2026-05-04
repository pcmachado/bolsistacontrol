<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Unit;

class FinancialAlertService
{
    public function getAlerts(int $month, int $year, $user)
    {
        return [
            ...$this->pendingPaymentsAlert($month, $year, $user),
            ...$this->unitsWithoutPaymentsAlert($month, $year, $user),
            ...$this->financialDropAlert($month, $year, $user),
        ];
    }

    protected function pendingPaymentsAlert($month, $year, $user)
    {
        $limitDays = 7;

        $query = Payment::query()
            ->where('status', Payment::STATUS_SENT)
            ->whereDate('sent_at', '<=', now()->subDays($limitDays));

        // 🔒 Escopo por unidade (adjunto)
        if ($user->hasRole('coordenador_adjunto')) {
            $query->whereIn('unit_id', $user->units->pluck('id'));
        }

        $count = $query->count();

        if ($count === 0) {
            return [];
        }

        return [[
            'type' => 'warning',
            'title' => 'Pagamentos pendentes',
            'message' => "{$count} pagamento(s) aguardando execução há mais de {$limitDays} dias.",
            'action_url' => route('admin.payments.index', ['status' => Payment::STATUS_SENT]),
        ]];
    }

    protected function unitsWithoutPaymentsAlert($month, $year, $user)
    {
        $unitsQuery = Unit::query();

        if ($user->hasRole('coordenador_adjunto')) {
            $unitsQuery->whereIn('id', $user->units->pluck('id'));
        }

        $units = $unitsQuery->get();

        $alerts = [];

        foreach ($units as $unit) {
            $hasPayments = Payment::query()
                ->where('unit_id', $unit->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if (! $hasPayments) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Unidade sem pagamentos',
                    'message' => "A unidade {$unit->name} não possui pagamentos em {$month}/{$year}.",
                    'action_url' => route('admin.payments.batch.form'),
                ];
            }
        }

        return $alerts;
    }

    protected function financialDropAlert($month, $year, $user)
    {
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear = $month == 1 ? $year - 1 : $year;

        $currentQuery = Payment::query()->where('month', $month)->where('year', $year);
        $previousQuery = Payment::query()->where('month', $prevMonth)->where('year', $prevYear);

        if ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units->pluck('id');
            $currentQuery->whereIn('unit_id', $units);
            $previousQuery->whereIn('unit_id', $units);
        }

        $currentTotal = $currentQuery
            ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_CONFIRMED])
            ->sum('amount');

        $previousTotal = $previousQuery
            ->whereIn('status', [Payment::STATUS_PAID, Payment::STATUS_CONFIRMED])
            ->sum('amount');

        if ($previousTotal <= 0) {
            return [];
        }

        $variation = (($currentTotal - $previousTotal) / $previousTotal) * 100;

        if ($variation >= -20) {
            return [];
        }

        return [[
            'type' => 'danger',
            'title' => 'Queda financeira',
            'message' => sprintf(
                'Queda de %.2f%% em relação ao mês anterior.',
                abs($variation)
            ),
            'action_url' => route('admin.payments.dashboard'),
        ]];
    }
}
