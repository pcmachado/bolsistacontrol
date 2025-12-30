<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ScholarshipHolder;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create()
    {
        return view('admin.payments.create', [
            'scholarshipHolders' => ScholarshipHolder::with('user')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $holder = ScholarshipHolder::with(['user', 'unit', 'projects'])->findOrFail($data['scholarship_holder_id']);

        // evita duplicidade (exceto draft)
        $exists = Payment::where('scholarship_holder_id', $holder->id)
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->whereIn('status', [
                Payment::STATUS_SENT,
                Payment::STATUS_PAID,
                Payment::STATUS_CONFIRMED,
            ])
            ->exists();

        if ($exists) {
            return back()->withErrors('Já existe pagamento fechado para este período.');
        }

        $records = AttendanceRecord::approved()
            ->where('scholarship_holder_id', $holder->id)
            ->whereMonth('date', $data['month'])
            ->whereYear('date', $data['year'])
            ->get();

        if ($records->isEmpty()) {
            return back()->withErrors('Nenhuma frequência homologada encontrada.');
        }

        $totalHours = $records->sum('hours');
        $amount     = $records->sum('calculated_value');

        DB::transaction(function () use ($data, $holder, $totalHours, $amount) {

            Payment::updateOrCreate(
                [
                    'scholarship_holder_id' => $holder->id,
                    'month' => $data['month'],
                    'year' => $data['year'],
                ],
                [
                    'project_id' => optional($holder->projects->first())->id,
                    'unit_id'    => $holder->unit_id,
                    'total_hours'=> $totalHours,
                    'amount'     => $amount,
                    'status'     => Payment::STATUS_DRAFT,
                ]
            );
        });

        return back()->with('success', 'Pagamento gerado como rascunho.');
    }

    public function confirm(Payment $payment)
    {
        abort_if(! $payment->isPaid(), 403);

        if (!$payment->receipt_number) {
            $payment->receipt_number = Payment::generateReceiptNumber();
        }

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Pagamento confirmado e recibo gerado.');
    }

    public function send(Payment $payment)
    {
        abort_if(! $payment->isDraft(), 403);

        $payment->update([
            'status'  => Payment::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Pagamento enviado para execução financeira.');
    }

    public function markAsPaid(Payment $payment)
    {
        abort_if(! $payment->isSent(), 403);

        $payment->update([
            'status'          => Payment::STATUS_PAID,
            'paid_at'         => now(),
            'paid_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Pagamento marcado como pago.');
    }

}
