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
    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $end = (clone $start)->modify('+'.rand(2,4).' hours');

        return [
            'scholarship_holder_id' => ScholarshipHolder::factory(),
            'date' => Carbon::instance($start)->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'hours' => $end->diff($start)->h,
            'calculated_value' => $this->faker->randomFloat(2, 50, 200),
            'status' => $this->faker->randomElement(['draft', 'submitted', 'approved', 'rejected']),
            'observation' => $this->faker->sentence(),
            'submitted_at' => null,
            'approved' => false,
            'approved_by_user_id' => null,
            'rejection_reason' => null,
            'rejected_at' => null,
        ];
    }
}
