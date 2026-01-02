<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\FinancialClosure;
use App\Services\FinancialAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentExecutionController extends Controller
{
    /**
     * Pagamentos aguardando execução
     */
    public function index()
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::where('status', Payment::STATUS_SENT)
            ->with(['scholarshipHolder.user', 'project', 'unit'])
            ->orderBy('sent_at')
            ->get();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Marca pagamento como pago
     */
    public function pay(Request $request, Payment $payment)
    {
        $this->authorize('markAsPaid', $payment);

        if (FinancialClosure::isClosed(
            $payment->unit_id,
            $payment->month,
            $payment->year
        )) {
            abort(403, 'Período financeiro fechado.');
        }

        $data = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($payment, $data) {

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'paid_by_user_id' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            FinancialAuditService::log(
                'paid',
                'Payment',
                $payment->id,
                ['amount' => $payment->amount]
            );

            // 🔔 Notificação ao bolsista
            $payment->scholarshipHolder->user->notify(
                new \App\Notifications\IntelligentSystemAlert(
                    title: 'Pagamento realizado',
                    message: "Seu pagamento de {$payment->periodLabel()} foi realizado.",
                    level: 'success',
                    url: route('payments.my')
                )
            );
        });

        return back()->with('success', 'Pagamento marcado como pago.');
    }

    /**
     * Pagamento em lote
     */
    public function batchPay(Request $request)
    {
        $data = $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'exists:payments,id',
        ]);

        $payments = Payment::whereIn('id', $data['payment_ids'])
            ->where('status', Payment::STATUS_SENT)
            ->get();

        DB::transaction(function () use ($payments) {
            foreach ($payments as $payment) {

                if (FinancialClosure::isClosed(
                    $payment->unit_id,
                    $payment->month,
                    $payment->year
                )) {
                    continue;
                }

                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'paid_by_user_id' => auth()->id(),
                ]);

                FinancialAuditService::log(
                    'paid',
                    'Payment',
                    $payment->id
                );
            }
        });

        return back()->with('success', 'Pagamentos processados em lote.');
    }
}
