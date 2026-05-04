<?php

namespace Tests\Feature\Seeds;

use App\Models\Course;
use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se CourseSeeder cria cursos com institution_id
     */
    public function test_course_seeder_creates_courses_with_institution_id(): void
    {
        // CourseSeeder depende de InstitutionSeeder, então executar ambos
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\InstitutionSeeder']);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\CourseSeeder']);

        $courses = Course::all();

        // Deve ter criado cursos
        $this->assertGreaterThan(0, $courses->count());

        // Todos os cursos devem ter institution_id
        $coursesWithoutInstitution = $courses->filter(fn ($c) => $c->institution_id === null);
        $this->assertEmpty(
            $coursesWithoutInstitution,
            'Existem cursos sem institution_id'
        );
    }

    /**
     * Testa isolamento: projetos e cursos devem estar na mesma instituição
     */
    public function test_project_course_isolation_by_institution(): void
    {
        $this->artisan('db:seed');

        $projectCourses = Course::join(
            'project_course',
            'courses.id',
            '=',
            'project_course.course_id'
        )
            ->join('projects', 'project_course.project_id', '=', 'projects.id')
            ->select('projects.institution_id as project_institution_id', 'courses.institution_id as course_institution_id')
            ->get();

        // Validar que nenhuma mistura existe
        $mismatches = $projectCourses->filter(
            fn ($pc) => $pc->project_institution_id !== $pc->course_institution_id
        );

        $this->assertEmpty(
            $mismatches,
            'Existem projetos com cursos de instituições diferentes'
        );
    }

    /**
     * Testa distribuição de cursos por instituição
     */
    public function test_courses_distributed_equally_per_institution(): void
    {
        $this->artisan('db:seed');

        $coursesByInstitution = Course::select('institution_id')
            ->groupBy('institution_id')
            ->get();

        // Deve ter cursos em múltiplas instituições
        $this->assertGreaterThanOrEqual(
            2,
            $coursesByInstitution->count(),
            'Cursos não estão distribuídos em múltiplas instituições'
        );

        // Cada instituição deve ter o mesmo número de cursos
        $courseCountsPerInstitution = Course::selectRaw('institution_id, COUNT(*) as count')
            ->groupBy('institution_id')
            ->pluck('count')
            ->unique();

        $this->assertEquals(
            1,
            $courseCountsPerInstitution->count(),
            'Instituições têm números diferentes de cursos'
        );
    }

    /**
     * Testa relacionamento entre Course e Institution
     */
    public function test_course_institution_relationship(): void
    {
        $this->artisan('db:seed');

        $course = Course::first();

        $this->assertNotNull($course->institution_id);
        $this->assertNotNull($course->institution);
        $this->assertInstanceOf(Institution::class, $course->institution);
    }
}
