<?php

namespace Tests\Unit;

use App\Models\AttendanceSubmission;
use App\Models\Payment;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatusPresentationTest extends TestCase
{
    #[Test]
    public function helper_translates_known_statuses(): void
    {
        $this->assertSame('Rascunho', status_label('draft'));
        $this->assertSame('Enviado para pagamento', status_label('sent_to_payment'));
        $this->assertSame('Status Custom', status_label('status_custom'));
    }

    #[Test]
    public function models_expose_status_label_and_color_attributes(): void
    {
        $submission = new AttendanceSubmission(['status' => AttendanceSubmission::STATUS_APPROVED]);
        $payment = new Payment(['status' => Payment::STATUS_SENT]);

        $this->assertSame('Aprovado', $submission->status_label);
        $this->assertSame('success', $submission->status_color);
        $this->assertSame('Enviado para pagamento', $payment->status_label);
        $this->assertSame('warning', $payment->status_color);
    }
}
