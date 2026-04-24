<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification as Notification;
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
                ->role('coordenador-adjunto')
                ->get();

            foreach ($coordenadores as $coord) {
                $coord->notifications()->create([
                    'type' => 'pendencia',
                    'data' => [
                        'message' => "Existem {$records->count()} registros de frequência pendentes de homologação referentes a {$lastMonth->format('m/Y')}.",
                    ],
                ]);
            }
        }

        $this->info("Notificações de pendência enviadas para coordenadores.");
    }
}
