<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Policies\PaymentPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Notifications\IntelligentSystemAlert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class MyPaymentController extends Controller
{
    public function myPayments()
    {
        $holder = auth()->user()->scholarshipHolder;

        abort_unless($holder, 403);

        $payments = Payment::where('scholarship_holder_id', $holder->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('payments.my', compact('payments'));
    }

    public function confirm(Payment $payment)
    {
        $this->authorize('confirm', $payment);

        abort_unless(
            $payment->scholarship_holder_id === auth()->user()->scholarshipHolder->id,
            403
        );

        abort_if(! $payment->isPaid(), 403);

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
