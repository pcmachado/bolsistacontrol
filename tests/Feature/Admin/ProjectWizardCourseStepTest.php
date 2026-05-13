<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectWizardCourseStepTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_step_advances_when_selected_course_posts_active_checkbox_value(): void
    {
        $this->withoutMiddleware();

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Campus Teste',
        ]);
        $project = Project::factory()->create([
            'institution_id' => $institution->id,
            'wizard_step' => 'step3',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonth(),
        ]);
        $course = Course::factory()->create(['institution_id' => $institution->id]);
        $user = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);

        $response = $this->actingAs($user)->post(route('admin.projects.store.step3', $project), [
            'courses' => [
                $course->id => [
                    'selected' => '1',
                    'course_id' => (string) $course->id,
                    'active' => 'on',
                    'semester' => '1º',
                    'year' => (string) now()->year,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.projects.create.step4', $project));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_course', [
            'project_id' => $project->id,
            'course_id' => $course->id,
            'active' => true,
            'semester' => '1º',
            'year' => now()->year,
        ]);
        $this->assertSame('step4', $project->fresh()->wizard_step);
    }

    public function test_course_step_shows_validation_error_when_no_course_is_selected(): void
    {
        $this->withoutMiddleware();

        $institution = Institution::factory()->create();
        $project = Project::factory()->create([
            'institution_id' => $institution->id,
            'wizard_step' => 'step3',
        ]);
        $course = Course::factory()->create(['institution_id' => $institution->id]);
        $user = User::factory()->create(['institution_id' => $institution->id]);

        $response = $this->actingAs($user)
            ->from(route('admin.projects.create.step3', $project))
            ->post(route('admin.projects.store.step3', $project), [
                'courses' => [
                    $course->id => [
                        'course_id' => (string) $course->id,
                    ],
                ],
            ]);

        $response->assertRedirect(route('admin.projects.create.step3', $project));
        $response->assertSessionHasErrors('courses');
        $this->assertSame('step3', $project->fresh()->wizard_step);
    }
}
