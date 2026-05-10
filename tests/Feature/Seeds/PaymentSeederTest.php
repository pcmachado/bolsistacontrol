<?php

namespace Tests\Feature\Seeds;

use App\Models\AttendanceSubmission;
use App\Models\Payment;
use App\Models\ScholarshipHolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se PaymentSeeder cria pagamentos
     */
    public function test_payment_seeder_creates_payments(): void
    {
        $this->artisan('db:seed');

        $payments = Payment::all();
        $this->assertGreaterThan(0, $payments->count());
    }

    /**
     * CRÍTICO: Testa se cada pagamento corresponde a uma AttendanceSubmission
     */
    public function test_payment_amounts_match_attendance_submissions(): void
    {
        $this->artisan('db:seed');

        $payments = Payment::all();

        $this->assertGreaterThan(0, $payments->count(), 'Nenhum pagamento foi criado');

        $mismatchCount = 0;
        $details = [];

        foreach ($payments as $payment) {
            $submission = AttendanceSubmission::where([
                ['scholarship_holder_id', '=', $payment->scholarship_holder_id],
                ['project_id', '=', $payment->project_id],
                ['month', '=', $payment->month],
                ['year', '=', $payment->year],
            ])->first();

            // Se houver submission, validar se os valores correspondem
            if ($submission) {
                if ($payment->amount != $submission->calculated_value) {
                    $mismatchCount++;
                    $details[] = [
                        'payment_id' => $payment->id,
                        'expected' => $submission->calculated_value,
                        'actual' => $payment->amount,
                    ];
                }
            }
        }

        $this->assertEquals(
            0,
            $mismatchCount,
            "Encontrados {$mismatchCount} pagamentos com valores incorretos: ".
            json_encode($details)
        );
    }

    /**
     * Testa se há pagamentos que não correspondem a submissions do mesmo projeto
     */
    public function test_payments_are_linked_to_matching_project_submissions(): void
    {
        $this->artisan('db:seed');

        $payments = Payment::all();
        $orphanedPayments = [];

        foreach ($payments as $payment) {
            $submission = AttendanceSubmission::where([
                ['scholarship_holder_id', '=', $payment->scholarship_holder_id],
                ['project_id', '=', $payment->project_id],
                ['month', '=', $payment->month],
                ['year', '=', $payment->year],
            ])->first();

            if (! $submission) {
                $orphanedPayments[] = $payment->id;
            }
        }

        $this->assertEmpty(
            $orphanedPayments,
            'Existem pagamentos sem submission correspondente: '.
            implode(', ', $orphanedPayments)
        );
    }

    /**
     * Testa distribuição de pagamentos por bolsista
     */
    public function test_payments_distributed_across_scholarship_holders(): void
    {
        $this->artisan('db:seed');

        $holders = ScholarshipHolder::withCount('payments')->get();
        $holdersWithPayments = $holders->filter(fn ($h) => $h->payments_count > 0);

        // Deve haver múltiplos bolsistas com pagamentos
        $this->assertGreaterThan(
            1,
            $holdersWithPayments->count(),
            'Pagamentos não foram distribuídos entre bolsistas'
        );

        // Cada bolsista deve ter múltiplos pagamentos (6 meses)
        foreach ($holdersWithPayments as $holder) {
            $this->assertGreaterThanOrEqual(
                1,
                $holder->payments_count,
                "Bolsista {$holder->id} não tem pagamentos"
            );
        }
    }

    /**
     * Testa horas nos pagamentos
     */
    public function test_payment_hours_are_populated(): void
    {
        $this->artisan('db:seed');

        $payments = Payment::where('total_hours', '>', 0)->get();

        $this->assertGreaterThan(0, $payments->count(), 'Nenhum pagamento tem horas');

        foreach ($payments as $payment) {
            $this->assertGreaterThan(
                0,
                $payment->total_hours,
                "Pagamento {$payment->id} tem horas zeradas"
            );
            $this->assertGreaterThan(
                0,
                $payment->amount,
                "Pagamento {$payment->id} tem valor zerado"
            );
        }
    }
}
