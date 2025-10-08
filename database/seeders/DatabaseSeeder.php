<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            InstituitionSeeder::class,
            UnitSeeder::class,
            PositionSeeder::class,
            ScholarshipHolderSeeder::class,
            ProjectSeeder::class,
            ProjectScholarshipHolderSeeder::class,
            AttendanceRecordSeeder::class,
            FundingSourceSeeder::class,
            CourseSeeder::class,
            PositionProjectSeeder::class,
            ProjectFundingSourceSeeder::class,
            CourseScholarshipHolderSeeder::class,
            CourseProjectSeeder::class,
        ]);
    }
}
