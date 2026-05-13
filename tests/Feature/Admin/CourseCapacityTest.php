<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_be_created_with_student_capacity(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.courses.store'), [
            'name' => 'Curso com Capacidade',
            'capacity' => 35,
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'name' => 'Curso com Capacidade',
            'capacity' => 35,
        ]);
    }

    public function test_course_capacity_can_be_updated(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $course = Course::factory()->create(['capacity' => 20]);

        $response = $this->actingAs($user)->put(route('admin.courses.update', $course), [
            'name' => $course->name,
            'capacity' => 45,
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSame(45, $course->fresh()->capacity);
    }

    public function test_course_capacity_must_be_positive_when_present(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), [
                'name' => 'Curso sem capacidade válida',
                'capacity' => 0,
            ]);

        $response->assertRedirect(route('admin.courses.create'));
        $response->assertSessionHasErrors('capacity');
    }
}
