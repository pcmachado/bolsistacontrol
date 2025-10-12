<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScholarshipHolder;
use App\Models\AttendanceRecord;
use App\Models\Notification;
use Carbon\Carbon;

class NotifyLateAttendance extends Command
{
    protected $signature = 'attendance:notify-late';
    protected $description = 'Notifica bolsistas que não enviaram registros até o dia 5 do mês seguinte';

    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth();
        $start = $lastMonth->copy()->startOfMonth();
        $end = $lastMonth->copy()->endOfMonth();

        // Bolsistas que não têm registros enviados no mês anterior
        $holders = ScholarshipHolder::whereDoesntHave('attendanceRecords', function ($q) use ($start, $end) {
            $q->whereBetween('date', [$start, $end])
              ->where('status', 'submitted');
        })->get();

        foreach ($holders as $holder) {
            Notification::create([
                'scholarship_holder_id' => $holder->id,
                'type' => 'atraso',
                'message' => "Você não enviou seus registros de frequência referentes a {$lastMonth->format('m/Y')}.",
            ]);
        }

        $this->info("Notificações de atraso enviadas para {$holders->count()} bolsistas.");
    }
}
