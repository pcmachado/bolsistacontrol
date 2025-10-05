<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AttendanceRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define uma data aleatória nos últimos 30 dias
        $date = $this->faker->dateTimeBetween('-30 days');
        
        // Gera horas de início e fim realistas
        $startTime = Carbon::instance($date)->setHour($this->faker->numberBetween(8, 10))->setMinutes($this->faker->randomElement([0, 30]));
        $endTime = Carbon::instance($startTime)->addHours($this->faker->numberBetween(4, 8));

        // Calcula a diferença de horas
        $hours = $endTime->diffInHours($startTime);
        
        // Define um status aleatório
        $status = $this->faker->randomElement([
            AttendanceRecord::STATUS_DRAFT, 
            AttendanceRecord::STATUS_PENDING, 
            AttendanceRecord::STATUS_APPROVED, 
            AttendanceRecord::STATUS_REJECTED
        ]);

        $approverId = null;
        $rejectionReason = null;
        
        if ($status === AttendanceRecord::STATUS_APPROVED || $status === AttendanceRecord::STATUS_REJECTED) {
            // Pega um coordenador aleatório para ser o aprovador
            $approver = User::role('coordenador_adjunto')->inRandomOrder()->first();
            $approverId = $approver ? $approver->id : null;

            if ($status === AttendanceRecord::STATUS_REJECTED) {
                $rejectionReason = $this->faker->sentence();
            }
        }

        return [
            'scholarship_holder_id' => ScholarshipHolder::factory(), // Cria um bolsista se não for passado um
            'date' => $date,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'hours' => $hours,
            'observation' => $this->faker->paragraph(),
            'status' => $status,
            'submitted_at' => ($status !== AttendanceRecord::STATUS_DRAFT) ? $this->faker->dateTimeThisMonth() : null,
            'approved_by_user_id' => $approverId,
            'rejection_reason' => $rejectionReason,
        ];
    }
}
