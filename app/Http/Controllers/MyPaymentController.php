<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class MyPaymentController extends Controller
{
    public function index()
    {
        $holder = auth()->user()->scholarshipHolder;

        if (! $holder) {
            abort(403, 'Usuário não é bolsista.');
        }

        $payments = Payment::where('scholarship_holder_id', $holder->id)
            ->with(['project', 'unit', 'paidBy'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('payments.my', compact('payments'));
    }

    public function confirm(Payment $payment)
    {
        $this->authorize('confirm', $payment);

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Recebimento confirmado com sucesso.');
    }

    public function receipt(Payment $payment)
    {
        $this->authorize('viewReceipt', $payment);

        if (!$payment->receipt_number) {
            abort(404, 'Recibo ainda não gerado.');
        }

        $html = view('payments.receipt_pdf', compact('payment'))->render();

        return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->stream("recibo_{$payment->receipt_number}.pdf");
    }

}
