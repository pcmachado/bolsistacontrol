<?php

namespace App\Http\Controllers;

use App\DataTables\PaymentDataTable;
use App\Models\Payment;
use App\Notifications\IntelligentSystemAlert;
use Barryvdh\DomPDF\Facade\Pdf;


class MyPaymentController extends Controller
{
    public function myPayments(PaymentDataTable $dataTable)
    {
        $dataTable->mode = 'my';

        return $dataTable->render('payments.my');
    }

    public function confirm(Payment $payment)
    {
        $this->authorize('confirm', $payment);

        if (! $payment->receipt_number) {
            $payment->receipt_number = Payment::generateReceiptNumber();
        }

        if (!$payment->receipt_hash) {
            $payment->receipt_hash = $this->generateReceiptHash($payment);
        }

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $payment->paidBy?->notify(
            new IntelligentSystemAlert(
                title: 'Pagamento confirmado',
                message: "O bolsista confirmou o pagamento {$payment->periodLabel()}",
                level: 'info'
            )
        );

        return back()->with('success', 'Pagamento confirmado com sucesso.');
    }

    public function receipt(Payment $payment)
    {
        $this->authorize('view', $payment);

        if (!$payment->isConfirmed()) {
            abort(403, 'Recibo disponível somente após confirmação.');
        }

        $payment->load([
            'scholarshipHolder.user',
            'project',
            'unit',
            'paidBy',
        ]);

        return Pdf::loadView(
            'payments.receipt',
            compact('payment')
        )->stream(
            'recibo_'.$payment->periodLabel().'.pdf'
        );
    }

    private function generateReceiptHash(Payment $payment): string
    {
        $base = implode('|', [
            $payment->id,
            $payment->scholarship_holder_id,
            $payment->project_id,
            $payment->amount,
            $payment->month,
            $payment->year,
            config('app.key'), // aumenta segurança
        ]);

        return hash('sha256', $base);
    }

}
