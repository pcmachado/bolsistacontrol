<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

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
        abort_unless(
            $payment->scholarship_holder_id === auth()->user()->scholarshipHolder->id,
            403
        );

        abort_if(! $payment->isPaid(), 403);

        if (! $payment->receipt_number) {
            $payment->receipt_number = Payment::generateReceiptNumber();
        }

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Pagamento confirmado com sucesso.');
    }

    public function receipt(Payment $payment)
    {
        $this->authorize('view', $payment);

        $pdf = \PDF::loadView('payments.receipt', compact('payment'));

        return $pdf->download(
            "recibo_{$payment->receipt_number}.pdf"
        );
    }

}
