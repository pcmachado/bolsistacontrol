<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentExecutionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::where('status', Payment::STATUS_SENT)
            ->with(['scholarshipHolder.user', 'project', 'unit'])
            ->orderBy('sent_at')
            ->get();

        return view('admin.payments.pending', compact('payments'));
    }

    public function pay(Request $request, Payment $payment)
    {
        $this->authorize('markAsPaid', $payment);

        $data = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'paid_by_user_id' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Notificação ao bolsista
        $payment->scholarshipHolder->user->notify(
            new \App\Notifications\IntelligentSystemAlert(
                title: 'Pagamento realizado',
                message: "Seu pagamento referente a {$payment->periodLabel()} foi realizado.",
                level: 'success',
                url: route('payments.my')
            )
        );

        return back()->with('success', 'Pagamento marcado como pago.');
    }
}
