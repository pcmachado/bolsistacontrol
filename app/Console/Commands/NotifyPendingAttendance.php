<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class NotifyPendingAttendance extends Command
{
    protected $signature = 'attendance:notify-pending';
    protected $description = 'Notifica coordenadores que não homologaram registros até o dia 10 do mês seguinte';

    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth();
        $start = $lastMonth->copy()->startOfMonth();
        $end = $lastMonth->copy()->endOfMonth();

        // Registros enviados mas não homologados
        $pending = AttendanceRecord::whereBetween('date', [$start, $end])
            ->where('status', 'submitted')
            ->get();

        // Agrupar por unidade
        $grouped = $pending->groupBy(fn($r) => $r->scholarshipHolder->unit_id);

        foreach ($grouped as $unitId => $records) {
            // Coordenadores adjuntos da unidade
            $coordenadores = User::where('unit_id', $unitId)
                ->whereHas('positions', fn($q) => $q->where('name', 'Coordenador Adjunto'))
                ->get();

            foreach ($coordenadores as $coord) {
                Notification::create([
                    'scholarship_holder_id' => null, // notificação para usuário
                    'type' => 'pendencia',
                    'message' => "Existem {$records->count()} registros de frequência pendentes de homologação referentes a {$lastMonth->format('m/Y')}.",
                ]);
            }
        }

        $this->info("Notificações de pendência enviadas para coordenadores.");
    }
}
