<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\AttendanceSubmitted;
use App\Notifications\PaymentStatusChanged;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationRecipientRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'coordenador_adjunto',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'bolsista',
        ] as $role) {
            Role::findOrCreate($role);
        }
    }

    public function test_submission_notification_goes_only_to_unit_coordinator(): void
    {
        Notification::fake();

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $otherUnit = Unit::factory()->create(['institution_id' => $institution->id]);

        $unitCoordinator = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $unitCoordinator->assignRole('coordenador_adjunto');

        $otherCoordinator = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $otherUnit->id,
        ]);
        $otherCoordinator->assignRole('coordenador_adjunto');

        $generalCoordinator = User::factory()->create(['institution_id' => $institution->id]);
        $generalCoordinator->assignRole('coordenador_geral');

        $submitter = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $submitter->assignRole('bolsista');

        app(NotificationService::class)->sendEventNotification(
            'submission_submitted',
            [
                'title' => 'Nova Submissão de Frequência',
                'message' => 'Submissão enviada.',
                'unit_id' => $unit->id,
                'submitter_user_id' => $submitter->id,
                'scholarship_holder_user_id' => $submitter->id,
            ],
            null,
            $institution->id
        );

        Notification::assertSentTo($unitCoordinator, AttendanceSubmitted::class);
        Notification::assertNotSentTo($otherCoordinator, AttendanceSubmitted::class);
        Notification::assertNotSentTo($generalCoordinator, AttendanceSubmitted::class);
    }

    public function test_administrative_unit_submission_from_adjoint_coordinator_goes_to_general_coordinator(): void
    {
        Notification::fake();

        $institution = Institution::factory()->create();
        $administrativeUnit = Unit::factory()->create([
            'institution_id' => $institution->id,
            'is_administrative' => true,
        ]);

        $generalCoordinator = User::factory()->create(['institution_id' => $institution->id]);
        $generalCoordinator->assignRole('coordenador_geral');

        $otherInstitutionGeneral = User::factory()->create();
        $otherInstitutionGeneral->assignRole('coordenador_geral');

        $submitter = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $administrativeUnit->id,
        ]);
        $submitter->assignRole('coordenador_adjunto_geral');

        app(NotificationService::class)->sendEventNotification(
            'submission_submitted',
            [
                'title' => 'Nova Submissão de Frequência',
                'message' => 'Submissão administrativa enviada.',
                'unit_id' => $administrativeUnit->id,
                'submitter_user_id' => $submitter->id,
                'scholarship_holder_user_id' => $submitter->id,
            ],
            null,
            $institution->id
        );

        Notification::assertSentTo($generalCoordinator, AttendanceSubmitted::class);
        Notification::assertNotSentTo($otherInstitutionGeneral, AttendanceSubmitted::class);
    }

    public function test_payment_sent_notification_goes_to_institution_financial_adjoint_general_coordinators(): void
    {
        Notification::fake();

        $institution = Institution::factory()->create();
        $otherInstitution = Institution::factory()->create();

        $financialCoordinator = User::factory()->create(['institution_id' => $institution->id]);
        $financialCoordinator->assignRole('coordenador_adjunto_geral');

        $otherFinancialCoordinator = User::factory()->create(['institution_id' => $otherInstitution->id]);
        $otherFinancialCoordinator->assignRole('coordenador_adjunto_geral');

        $generalCoordinator = User::factory()->create(['institution_id' => $institution->id]);
        $generalCoordinator->assignRole('coordenador_geral');

        app(NotificationService::class)->sendEventNotification(
            'payment_sent_to_financial',
            [
                'title' => 'Pagamento enviado ao financeiro',
                'message' => 'Pagamento enviado para execução.',
                'payment_id' => 10,
                'new_status' => 'sent_to_payment',
            ],
            null,
            $institution->id
        );

        Notification::assertSentTo($financialCoordinator, PaymentStatusChanged::class);
        Notification::assertNotSentTo($otherFinancialCoordinator, PaymentStatusChanged::class);
        Notification::assertNotSentTo($generalCoordinator, PaymentStatusChanged::class);
    }
}
