<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*// Lista das tabelas que vamos resetar (orde negativa — dependentes primeiro)
        $tables = [
            'attendance_records',
            'project_scholarship_holders',
            'class_offerings',
            'project_funding_source',
            'project_scholarship_holders',
            'project_scholarship_holders', // duplicated safe
            'scholarship_holders',
            'project_scholarship_holders',
            'institution_users',
            'projects',
            'units',
            'funding_sources',
            'courses',
            'project_positions',
            'users',
            'institutions',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
        ];

        foreach ($tables as $table) {
            DB::table($table)->delete();
            try {
                DB::statement("ALTER TABLE `$table` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {
                // ignore if table doesn't exist or cannot reset
            }
        }*/

        /*
        RolesAndPermissionsSeeder::class,
        UserSeeder::class,
        institutionSeeder::class,
        UnitSeeder::class,
        PositionSeeder::class,
        ScholarshipHolderSeeder::class,
        ProjectSeeder::class,
        ProjectScholarshipHolderSeeder::class,
        AttendanceRecordSeeder::class,
        FundingSourceSeeder::class,
        CourseSeeder::class,
        ProjectFundingSourceSeeder::class,
        CourseScholarshipHolderSeeder::class,
        ClassOfferingSeeder::class,
        ProjectPositionSeeder::class,
        InstitutionUserSeeder::class,
        */

        // Roda os seeders na ordem correta
        $this->call([
            RolesAndPermissionsSeeder::class,
            InstitutionSeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
            //InstitutionUserSeeder::class,
            ScholarshipHolderSeeder::class,
            ProjectSeeder::class,
            ProjectScholarshipHolderSeeder::class,
            AttendanceRecordSeeder::class,
            FundingSourceSeeder::class,
            CourseSeeder::class,
            ProjectFundingSourceSeeder::class,
            CourseScholarshipHolderSeeder::class,
            ClassOfferingSeeder::class,
            ProjectPositionSeeder::class,
            NotificationTestSeeder::class,
        ]);
    }
}
