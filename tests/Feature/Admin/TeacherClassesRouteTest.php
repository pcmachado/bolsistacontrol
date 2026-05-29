<?php

namespace Tests\Feature\Admin;

use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Institution;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Student;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherClassesRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_classes_index_loads_teacher_units_without_institution_scope_recursion(): void
    {
        Role::create(['name' => 'professor']);

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $course = Course::factory()->create(['institution_id' => $institution->id]);
        $project = Project::factory()->create(['institution_id' => $institution->id]);

        $user = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $user->assignRole('professor');

        $holder = ScholarshipHolder::factory()->create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
        ]);

        $offering = ClassOffering::factory()->create([
            'course_id' => $course->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'name' => 'Turma Teste',
        ]);

        $discipline = Discipline::query()->create([
            'course_id' => $course->id,
            'name' => 'Disciplina Teste',
            'workload' => 40,
            'active' => true,
        ]);

        ClassOfferingDiscipline::query()->create([
            'class_offering_id' => $offering->id,
            'discipline_id' => $discipline->id,
            'teacher_id' => $holder->id,
            'workload' => 40,
        ]);

        $this->actingAs($user)
            ->get(route('teacher.classes'))
            ->assertOk()
            ->assertSee('Turma Teste');
    }

    public function test_teacher_monthly_records_use_configured_hours_per_day_to_calculate_planned_classes(): void
    {
        Role::create(['name' => 'professor']);

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $course = Course::factory()->create(['institution_id' => $institution->id]);
        $project = Project::factory()->create(['institution_id' => $institution->id]);

        $user = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $user->assignRole('professor');

        $holder = ScholarshipHolder::factory()->create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
        ]);

        $offering = ClassOffering::factory()->create([
            'course_id' => $course->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'name' => 'Turma Frequencia',
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]);

        $discipline = Discipline::query()->create([
            'course_id' => $course->id,
            'name' => 'Disciplina Frequencia',
            'workload' => 40,
            'active' => true,
        ]);

        $pivot = ClassOfferingDiscipline::query()->create([
            'class_offering_id' => $offering->id,
            'discipline_id' => $discipline->id,
            'teacher_id' => $holder->id,
            'workload' => 40,
            'planned_total_hours' => 40,
            'hours_per_day' => 4,
        ]);

        $student = Student::factory()->create(['name' => 'Aluno Teste']);
        $offering->students()->attach($student->id);

        $this->actingAs($user)
            ->get(route('teacher.classes.show', [
                $offering,
                'discipline_id' => $discipline->id,
                'month' => '2026-05',
            ]))
            ->assertOk()
            ->assertSee('10 dias/aulas')
            ->assertSee('10 / 10');

        $this->actingAs($user)
            ->post(route('teacher.classes.monthly.save', $offering), [
                'discipline_id' => $discipline->id,
                'records' => [
                    $student->id => [
                        '2026-05' => [
                            'absences' => 2,
                            'justified' => 1,
                        ],
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('student_discipline_month_records', [
            'student_id' => $student->id,
            'class_offering_id' => $offering->id,
            'discipline_id' => $discipline->id,
            'class_offering_discipline_id' => $pivot->id,
            'month' => 5,
            'year' => 2026,
            'total_classes' => 10,
            'classes_in_month' => 10,
            'absences' => 2,
            'justified_absences' => 1,
            'attended_classes' => 8,
        ]);
    }

    public function test_teacher_can_access_own_courses_and_disciplines(): void
    {
        Role::create(['name' => 'professor']);

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $course = Course::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Curso do Professor',
        ]);
        $project = Project::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Projeto do Professor',
        ]);

        $user = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $user->assignRole('professor');

        $holder = ScholarshipHolder::factory()->create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
        ]);

        $offering = ClassOffering::factory()->create([
            'course_id' => $course->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'name' => 'Turma do Professor',
        ]);

        $discipline = Discipline::query()->create([
            'course_id' => $course->id,
            'name' => 'Disciplina do Professor',
            'workload' => 30,
            'active' => true,
        ]);

        ClassOfferingDiscipline::query()->create([
            'class_offering_id' => $offering->id,
            'discipline_id' => $discipline->id,
            'teacher_id' => $holder->id,
            'workload' => 30,
            'planned_total_hours' => 30,
            'hours_per_day' => 3,
        ]);

        $this->actingAs($user)
            ->get(route('teacher.courses'))
            ->assertOk()
            ->assertSee('Curso do Professor')
            ->assertSee('Turma do Professor')
            ->assertSee('Disciplina do Professor');

        $this->actingAs($user)
            ->get(route('teacher.disciplines'))
            ->assertOk()
            ->assertSee('Disciplina do Professor')
            ->assertSee('Curso do Professor')
            ->assertSee('10');
    }
}
