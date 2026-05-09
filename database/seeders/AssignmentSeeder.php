<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\ClassOffering;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->migrateProjects();
        $this->migrateCourses();
        $this->migrateOfferings();
        $this->migrateTeachers();
        $this->createRoleBasedAssignments();
    }

    // ... existing methods ...

    protected function createRoleBasedAssignments(): void
    {
        ScholarshipHolder::with('user')->chunk(50, function ($holders) {
            foreach ($holders as $holder) {
                if (! $holder->user) {
                    continue;
                }

                $role = $holder->user->getRoleNames()->first();

                if ($role && ! $holder->user->assignments()->where('assignment_type', $role)->exists()) {
                    Assignment::create([
                        'user_id' => $holder->user_id,
                        'assignment_type' => $role,
                        'unit_id' => $holder->unit_id,
                        'active' => true,
                    ]);
                }
            }
        });
    }

    protected function migrateProjects(): void
    {
        ScholarshipHolder::with(['user', 'projects'])->chunk(50, function ($holders) {
            foreach ($holders as $holder) {
                if (! $holder->user) {
                    continue;
                }

                foreach ($holder->projects as $project) {
                    Assignment::firstOrCreate([
                        'user_id' => $holder->user_id,
                        'project_id' => $project->id,
                        'assignment_type' => Assignment::TYPE_COORDENADOR_ADJUNTO_GERAL,
                    ], [
                        'unit_id' => $holder->unit_id,
                        'position_id' => $project->pivot->position_id,
                        'start_date' => $project->pivot->start_date,
                        'end_date' => $project->pivot->end_date,
                        'active' => true,
                    ]);
                }
            }
        });
    }

    protected function migrateCourses(): void
    {
        ScholarshipHolder::with(['user', 'courses'])->chunk(50, function ($holders) {
            foreach ($holders as $holder) {
                if (! $holder->user) {
                    continue;
                }

                foreach ($holder->courses as $course) {
                    Assignment::firstOrCreate([
                        'user_id' => $holder->user_id,
                        'course_id' => $course->id,
                        'assignment_type' => $course->pivot->role ?? Assignment::TYPE_APOIO,
                    ], [
                        'unit_id' => $holder->unit_id,
                        'active' => true,
                    ]);
                }
            }
        });
    }

    protected function migrateOfferings(): void
    {
        ScholarshipHolder::with(['user', 'classOfferings'])->chunk(50, function ($holders) {
            foreach ($holders as $holder) {
                if (! $holder->user) {
                    continue;
                }

                foreach ($holder->classOfferings as $offering) {
                    Assignment::firstOrCreate([
                        'user_id' => $holder->user_id,
                        'class_offering_id' => $offering->id,
                        'assignment_type' => $offering->pivot->role ?? Assignment::TYPE_APOIO,
                    ], [
                        'course_id' => $offering->course_id,
                        'unit_id' => $offering->unit_id,
                        'active' => true,
                    ]);
                }
            }
        });
    }

    protected function migrateTeachers(): void
    {
        ClassOffering::with(['disciplines'])->chunk(50, function ($offerings) {
            foreach ($offerings as $offering) {
                foreach ($offering->disciplines as $discipline) {
                    if (! $discipline->teacher_id) {
                        continue;
                    }

                    Assignment::firstOrCreate([
                        'user_id' => $discipline->teacher_id,
                        'class_offering_id' => $offering->id,
                        'assignment_type' => Assignment::TYPE_PROFESSOR,
                    ], [
                        'course_id' => $offering->course_id,
                        'unit_id' => $offering->unit_id,
                        'active' => true,
                    ]);
                }
            }
        });
    }
}
