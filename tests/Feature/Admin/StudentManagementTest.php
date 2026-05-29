<?php

namespace Tests\Feature\Admin;

use App\Models\ClassOffering;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student_with_contact_fields_and_class_offering(): void
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        $offering = ClassOffering::factory()->create([
            'name' => 'Turma Teste',
        ]);

        $response = $this->actingAs($user)
            ->post(route('admin.students.store'), [
                'class_offering_ids' => [$offering->id],
                'name' => 'Aluno Teste',
                'cpf' => '12345678901',
                'email' => 'aluno@example.com',
                'phone' => '(54) 99999-0000',
                'payment_type' => 'pix',
                'pix_key' => 'aluno@example.com',
            ]);

        $response->assertRedirect(route('admin.students.index'));

        $student = Student::query()->where('email', 'aluno@example.com')->first();

        $this->assertNotNull($student);
        $this->assertSame('(54) 99999-0000', $student->phone);
        $this->assertTrue(
            $student->classOfferings()
                ->whereKey($offering->id)
                ->exists()
        );
    }

    public function test_student_store_accepts_legacy_single_class_offering_field(): void
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        $offering = ClassOffering::factory()->create([
            'name' => 'Turma Legada',
        ]);

        $response = $this->actingAs($user)
            ->post(route('admin.students.store'), [
                'class_offering_id' => $offering->id,
                'name' => 'Aluno Formulario Antigo',
                'payment_type' => 'pix',
                'pix_key' => 'legacy@example.com',
            ]);

        $response->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', [
            'name' => 'Aluno Formulario Antigo',
        ]);
    }
}
