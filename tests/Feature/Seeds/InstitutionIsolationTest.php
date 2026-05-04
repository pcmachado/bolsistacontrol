<?php

namespace Tests\Feature\Seeds;

use App\Models\Course;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se coordenadores estão vinculados à instituição correta
     */
    public function test_coordinators_linked_to_correct_institution(): void
    {
        $this->artisan('db:seed');

        $coordinators = User::role('coordenador_geral')->get();

        $this->assertGreaterThan(0, $coordinators->count(), 'Nenhum coordenador geral foi criado');

        foreach ($coordinators as $coordinator) {
            // Deve ter institution_id
            $this->assertNotNull(
                $coordinator->institution_id,
                "Coordenador {$coordinator->name} não tem institution_id"
            );

            // Deve estar vinculado à instituição correspondente
            $this->assertTrue(
                $coordinator->institutions->contains('id', $coordinator->institution_id),
                "Coordenador {$coordinator->name} não está vinculado à sua instituição"
            );
        }
    }

    /**
     * Testa se cursos estão isolados por instituição
     */
    public function test_courses_isolated_by_institution(): void
    {
        $this->artisan('db:seed');

        $institutions = Institution::with('courses')->get();

        foreach ($institutions as $institution) {
            $courses = $institution->courses ?? [];

            $this->assertGreaterThan(
                0,
                count($courses),
                "Instituição {$institution->name} não tem cursos"
            );

            // Todos os cursos devem ter institution_id correto
            foreach ($courses as $course) {
                $this->assertEquals(
                    $institution->id,
                    $course->institution_id,
                    "Curso {$course->name} não está vinculado à instituição correta"
                );
            }
        }
    }

    /**
     * Testa se não há cursos globais (sem institution_id)
     */
    public function test_no_global_courses_exist(): void
    {
        $this->artisan('db:seed');

        $globalCourses = Course::whereNull('institution_id')->get();

        $this->assertEmpty(
            $globalCourses,
            "Existem {$globalCourses->count()} cursos sem institution_id"
        );
    }

    /**
     * Testa se dados estão isolados por instituição
     */
    public function test_institution_data_isolation(): void
    {
        $this->artisan('db:seed');

        $institutions = Institution::with('courses', 'projects')->get();
        $this->assertGreaterThanOrEqual(2, $institutions->count());

        // Pegar 2 instituições diferentes
        $inst1 = $institutions->first();
        $inst2 = $institutions->last();

        if ($inst1->id === $inst2->id) {
            $this->markTestSkipped('Precisa de pelo menos 2 instituições diferentes');
        }

        // Cursos de Inst1 não devem aparecer em projetos de Inst2
        $inst1Courses = $inst1->courses->pluck('id')->toArray();
        $inst2Projects = $inst2->projects ?? [];

        foreach ($inst2Projects as $project) {
            // Carregar cursos do projeto
            $project->load('courses');
            $projectCourseIds = $project->courses->pluck('id')->toArray();
            $overlap = array_intersect($inst1Courses, $projectCourseIds);

            $this->assertEmpty(
                $overlap,
                "Cursos de {$inst1->name} aparecem em projetos de {$inst2->name}"
            );
        }
    }

    /**
     * Testa se todos os usuários estão vinculados a uma instituição
     */
    public function test_all_institutional_users_have_institution_id(): void
    {
        $this->artisan('db:seed');

        // Usuários com roles institucionais (não admin superadmin)
        $institutionalUsers = User::whereNotIn('email', [
            'superadmin@bolsista.com',
            'admin@bolsista.com',
        ])->get();

        foreach ($institutionalUsers as $user) {
            // Deve ter institution_id ou unit_id (que leva a institution_id)
            $hasInstitution = $user->institution_id ||
                ($user->unit && $user->unit->institution_id);

            $this->assertTrue(
                $hasInstitution,
                "Usuário {$user->name} não está vinculado a uma instituição"
            );
        }
    }
}
