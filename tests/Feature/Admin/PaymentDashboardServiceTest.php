<?php

namespace Tests\Feature\Admin;

use App\Models\FundingSource;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Services\PaymentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_funding_source_total_when_project_allocation_is_blank(): void
    {
        $institution = Institution::factory()->create();
        $user = $this->adminUser($institution);

        $project = Project::factory()->create([
            'institution_id' => $institution->id,
        ]);
        $fundingSource = FundingSource::factory()->create([
            'total_amount' => 10000,
            'used_amount' => 0,
        ]);

        DB::table('project_funding_source')->insert([
            'project_id' => $project->id,
            'funding_source_id' => $fundingSource->id,
            'allocated_amount' => null,
            'used_amount' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = app(PaymentDashboardService::class)->data($user, [
            'month' => 5,
            'year' => 2026,
            'project_id' => $project->id,
        ]);

        $this->assertSame(10000.0, $data['budgetAllocated']);
        $this->assertSame(10000.0, $data['budgetAvailable']);
        $this->assertSame(0.0, $data['budgetUsed']);
        $this->assertCount(1, $data['projectBudgetSummaries']);
        $this->assertSame(10000.0, $data['projectBudgetSummaries']->first()['available_total']);
    }

    public function test_dashboard_budget_unit_filter_includes_project_direct_unit(): void
    {
        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $user = $this->adminUser($institution);

        $project = Project::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $fundingSource = FundingSource::factory()->create([
            'total_amount' => 8000,
            'used_amount' => 0,
        ]);

        DB::table('project_funding_source')->insert([
            'project_id' => $project->id,
            'funding_source_id' => $fundingSource->id,
            'allocated_amount' => 6000,
            'used_amount' => 1500,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = app(PaymentDashboardService::class)->data($user, [
            'month' => 5,
            'year' => 2026,
            'unit_id' => $unit->id,
        ]);

        $this->assertSame(6000.0, $data['budgetAllocated']);
        $this->assertSame(1500.0, $data['budgetUsed']);
        $this->assertSame(4500.0, $data['budgetAvailable']);
    }

    private function adminUser(Institution $institution): User
    {
        $user = User::factory()->create(['institution_id' => $institution->id]);
        Role::findOrCreate('admin', 'web');
        $user->assignRole('admin');

        return $user;
    }
}
