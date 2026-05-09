<?php

namespace App\Http\Controllers;

use App\DataTables\PaymentDataTable;
use App\Models\Payment;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $oldStatus = $payment->status;

        if (! $payment->receipt_number) {
            $payment->receipt_number = Payment::generateReceiptNumber();
        }

        if (! $payment->receipt_hash) {
            $payment->receipt_hash = $this->generateReceiptHash($payment);
        }

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        // Notificação usando o sistema avançado
        $notificationService = app(NotificationService::class);
        $notificationService->sendEventNotification(
            'payment_status_changed',
            [
                'title' => 'Pagamento Confirmado',
                'message' => "O bolsista {$payment->scholarshipHolder->user->name} confirmou o pagamento de {$payment->periodLabel()}",
                'level' => 'success',
                'payment_id' => $payment->id,
                'old_status' => $oldStatus,
                'new_status' => Payment::STATUS_CONFIRMED,
                'url' => route('payments.show', $payment),
                'scholarship_holder_name' => $payment->scholarshipHolder->user->name,
                'period' => $payment->periodLabel(),
                'amount' => number_format($payment->amount, 2, ',', '.'),
            ],
            $payment->project_id,
            $payment->unit->institution_id ?? null
        );

        return back()->with('success', 'Pagamento confirmado com sucesso.');
    }

    public function receipt(Payment $payment)
    {
        $this->authorize('view', $payment);

        if (! $payment->isConfirmed()) {
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
            [
                'payment' => $payment,
                'isPdf' => true,
            ]
        )->stream(
            'recibo_'.\Str::slug(
                $payment->scholarshipHolder->user->name.'_'
                .$payment->receipt_number
                .$payment->safePeriodLabel()
            ).'.pdf'
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

        return hash_hmac('sha256', $base, config('app.key'));
    }

    public function reportMy(Request $request)
    {
        $user = Auth::user();

        $query = Payment::with(['scholarshipHolder.user', 'unit']);

        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, $user, 'self');

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);

            $query->where('year', $year)
                ->where('month', $month);
        } elseif ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $payments = $query->get();

        $total = $payments->sum('amount');

        $isPdf = $request->boolean('pdf');

        if ($isPdf) {
            $pdf = Pdf::loadView('payments.reports.my', compact('payments', 'total', 'isPdf'));

            return $pdf->stream('relatorio_pagamentos.pdf');
        }

        return view('payments.reports.my', compact('payments', 'total', 'isPdf'));
    }
}
