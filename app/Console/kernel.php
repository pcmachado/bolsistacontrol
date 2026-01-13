<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $service = app(\App\Services\IntelligentNotificationService::class);

            foreach (\App\Models\ClassOffering::with('unit')->get() as $offering) {
                $service->checkDisciplineDelays($offering);
                $service->checkNoRecentClasses($offering);
            }

        })->dailyAt('06:00'); // envia todo dia às 6h
    }
}
