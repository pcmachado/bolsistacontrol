<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Institution;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_course_with_project_and_institution(): void
    {
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create(['institution_id' => Institution::factory()]);
        $user->assignRole('admin');

        $institution = Institution::factory()->create();
        $project = Project::factory()->create([
            'institution_id' => $institution->id,
        ]);

        $response = $this->actingAs($user)->post(route('admin.courses.store'), [
            'name' => 'Curso de Teste',
            'description' => 'Descrição do curso de teste',
            'duration_hours' => 80,
            'prerequisites' => 'Nenhum',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'active' => '1',
            'institution_id' => $institution->id,
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('admin.courses.index'));

        $course = Course::where('name', 'Curso de Teste')->first();

        $this->assertNotNull($course);
        $this->assertSame('Descrição do curso de teste', $course->description);
        $this->assertSame(80, $course->duration_hours);
        $this->assertTrue($course->active);
        $this->assertSame($institution->id, $course->institution_id);
        $this->assertTrue($course->projects->contains($project));
    }

    public function test_institution_scoped_user_sees_course_without_class_offerings(): void
    {
        Role::firstOrCreate(['name' => 'admin']);

        $institution = Institution::factory()->create();
        $user = User::factory()->create(['institution_id' => $institution->id]);
        $user->assignRole('admin');

        Course::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Curso sem Turma',
        ]);

        $response = $this->actingAs($user)->get(route('admin.courses.index'));

        $response->assertStatus(200);
        $response->assertSee('Curso sem Turma');
    }
}
