<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestPaymentMultiprojetoTest extends TestCase
{
    use RefreshDatabase;

    public function test_mypayments_supports_multiproject_context()
    {
        // Criar dados manualmente para evitar problemas com factories
        $institution = \App\Models\Institution::create([
            'name' => 'Instituição Teste',
            'acronym' => 'TEST',
            'cnpj' => '12345678000199',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'institution_id' => $institution->id,
        ]);
        $user->markEmailAsVerified();

        $user->institutions()->attach($institution->id);

        $holder = ScholarshipHolder::create([
            'user_id' => $user->id,
            'registration_number' => '12345',
            'name' => 'João Silva',
            'cpf' => '12345678901',
        ]);

        $project1 = Project::create([
            'name' => 'Projeto Alpha',
            'institution_id' => $institution->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        $project2 = Project::create([
            'name' => 'Projeto Beta',
            'institution_id' => $institution->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        $position = \App\Models\Position::create([
            'name' => 'Bolsista',
            'description' => 'Posição de bolsista',
        ]);

        $holder->projects()->attach([$project1->id, $project2->id], [
            'position_id' => $position->id,
            'start_date' => now()->subMonths(3),
        ]);

        $unit = \App\Models\Unit::create([
            'name' => 'Unidade Teste',
            'institution_id' => $institution->id,
            'city' => 'São Paulo',
            'address' => 'Rua Teste, 123',
        ]);

        // Criar pagamentos para diferentes projetos
        Payment::create([
            'scholarship_holder_id' => $holder->id,
            'project_id' => $project1->id,
            'unit_id' => $unit->id,
            'amount' => 1000.00,
            'month' => 1,
            'year' => 2024,
            'status' => 'confirmed',
        ]);

        Payment::create([
            'scholarship_holder_id' => $holder->id,
            'project_id' => $project2->id,
            'unit_id' => $unit->id,
            'amount' => 1500.00,
            'month' => 1,
            'year' => 2024,
            'status' => 'confirmed',
        ]);

        // Testar contexto projeto 1
        $response = $this->actingAs($user)
            ->get(route('payments.my', ['project_id' => $project1->id]));

        $response->assertStatus(200);
        $response->assertSee('Projeto Alpha'); // Aba ativa
        $response->assertSee('R$ 1.000,00'); // Valor do projeto 1
        $response->assertDontSee('R$ 1.500,00'); // Não mostra valor do projeto 2
    }

    public function test_mypayments_filters_by_month_and_project()
    {
        // Criar dados básicos
        $institution = \App\Models\Institution::create([
            'name' => 'Instituição Teste 2',
            'acronym' => 'TEST2',
            'cnpj' => '12345678000299',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
        ]);

        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'institution_id' => $institution->id,
        ]);
        $user->markEmailAsVerified();

        $user->institutions()->attach($institution->id);

        $holder = ScholarshipHolder::create([
            'user_id' => $user->id,
            'registration_number' => '12346',
            'name' => 'Maria Santos',
            'cpf' => '12345678902',
        ]);

        $project = Project::create([
            'name' => 'Projeto Teste',
            'institution_id' => $institution->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        $position = \App\Models\Position::create([
            'name' => 'Bolsista 2',
            'description' => 'Posição de bolsista 2',
        ]);

        $holder->projects()->attach($project->id, [
            'position_id' => $position->id,
            'start_date' => now()->subMonths(3),
        ]);

        $unit = \App\Models\Unit::create([
            'name' => 'Unidade Teste 2',
            'institution_id' => $institution->id,
            'city' => 'Rio de Janeiro',
            'address' => 'Rua Teste 2, 456',
        ]);

        // Criar pagamentos em meses diferentes
        Payment::create([
            'scholarship_holder_id' => $holder->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'amount' => 1000.00,
            'month' => 1,
            'year' => 2024,
            'status' => 'confirmed',
        ]);

        Payment::create([
            'scholarship_holder_id' => $holder->id,
            'project_id' => $project->id,
            'unit_id' => $unit->id,
            'amount' => 1200.00,
            'month' => 2,
            'year' => 2024,
            'status' => 'confirmed',
        ]);

        // Testar filtro por mês
        $response = $this->actingAs($user)
            ->get(route('payments.my', [
                'project_id' => $project->id,
                'month' => '2024-01',
            ]));

        $response->assertStatus(200);
        $response->assertSee('R$ 1.000,00');
        $response->assertDontSee('R$ 1.200,00');
    }

    public function test_mypayments_invalid_project_returns_403()
    {
        // Criar dados básicos
        $institution = \App\Models\Institution::create([
            'name' => 'Instituição Teste 3',
            'acronym' => 'TEST3',
            'cnpj' => '12345678000399',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
        ]);

        $user = User::create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
            'institution_id' => $institution->id,
        ]);
        $user->markEmailAsVerified();

        $user->institutions()->attach($institution->id);

        $holder = ScholarshipHolder::create([
            'user_id' => $user->id,
            'registration_number' => '12347',
            'name' => 'Pedro Oliveira',
            'cpf' => '12345678903',
        ]);

        $project = Project::create([
            'name' => 'Projeto Teste 3',
            'institution_id' => $institution->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        $position = \App\Models\Position::create([
            'name' => 'Bolsista 3',
            'description' => 'Posição de bolsista 3',
        ]);

        $holder->projects()->attach($project->id, [
            'position_id' => $position->id,
            'start_date' => now()->subMonths(3),
        ]);

        // Tentar acessar projeto não vinculado
        $otherProject = Project::create([
            'name' => 'Projeto Não Vinculado',
            'institution_id' => $institution->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        $response = $this->actingAs($user)
            ->get(route('payments.my', ['project_id' => $otherProject->id]));

        $response->assertStatus(403);
    }
}
