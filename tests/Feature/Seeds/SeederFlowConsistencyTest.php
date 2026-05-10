<?php

namespace Tests\Feature\Seeds;

use App\Models\AttendanceSubmission;
use App\Models\ClassOfferingSubmission;
use App\Models\FinancialClosure;
use App\Models\Institution;
use App\Models\Payment;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederFlowConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_ifrs_general_adjunct_coordinator_is_linked_to_both_ifrs_projects(): void
    {
        $this->artisan('db:seed');

        $institution = Institution::where('acronym', 'ifrs')->firstOrFail();
        $coordinator = User::where('email', 'cag@ifrs.example.com')->firstOrFail();
        $holder = $coordinator->scholarshipHolder;

        $this->assertNotNull($holder);

        $ifrsProjectIds = $institution->projects()->pluck('id')->sort()->values();
        $holderProjectIds = $holder->projects()
            ->where('institution_id', $institution->id)
            ->pluck('projects.id')
            ->sort()
            ->values();

        $this->assertCount(2, $ifrsProjectIds);
        $this->assertEquals($ifrsProjectIds, $holderProjectIds);
    }

    public function test_seeded_monthly_flows_are_closed_until_previous_month_and_open_for_current_month(): void
    {
        $this->artisan('db:seed');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $this->assertGreaterThan(0, AttendanceSubmission::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'draft')->count());
        $this->assertGreaterThan(0, ClassOfferingSubmission::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'draft')->count());
        $this->assertGreaterThan(0, Payment::where('month', $currentMonth)->where('year', $currentYear)->where('status', Payment::STATUS_DRAFT)->count());
        $this->assertGreaterThan(0, StudentPayment::where('month', $currentMonth)->where('year', $currentYear)->where('status', StudentPayment::STATUS_SENT)->count());

        if ($currentMonth > 1) {
            $this->assertGreaterThan(0, AttendanceSubmission::where('month', '<', $currentMonth)->where('year', $currentYear)->where('status', 'approved')->count());
            $this->assertGreaterThan(0, ClassOfferingSubmission::where('month', '<', $currentMonth)->where('year', $currentYear)->where('status', 'approved')->count());
            $this->assertGreaterThan(0, Payment::where('month', '<', $currentMonth)->where('year', $currentYear)->where('status', Payment::STATUS_CONFIRMED)->count());
            $this->assertGreaterThan(0, StudentPayment::where('month', '<', $currentMonth)->where('year', $currentYear)->where('status', StudentPayment::STATUS_PAID)->count());
            $this->assertGreaterThan(0, FinancialClosure::where('month', '<', $currentMonth)->where('year', $currentYear)->count());
        }

        $this->assertEquals(0, FinancialClosure::where('month', $currentMonth)->where('year', $currentYear)->count());
    }
}
