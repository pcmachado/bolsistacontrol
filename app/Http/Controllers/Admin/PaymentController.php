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

        $holder = ScholarshipHolder::with(['user', 'unit'])->findOrFail($data['scholarship_holder_id']);

        // Evita duplicidade
        $exists = Payment::where('scholarship_holder_id', $holder->id)
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors('Já existe um pagamento para este bolsista neste período.');
        }

        // Busca frequências homologadas
        $records = AttendanceRecord::approved()
            ->where('scholarship_holder_id', $holder->id)
            ->whereMonth('date', $data['month'])
            ->whereYear('date', $data['year'])
            ->get();

        if ($records->isEmpty()) {
            return back()->withErrors('Nenhuma frequência homologada encontrada para este período.');
        }

        $totalHours = $records->sum('hours');
        $amount     = $records->sum('calculated_value');

        DB::transaction(function () use ($data, $holder, $totalHours, $amount) {

            Payment::create([
                'scholarship_holder_id' => $holder->id,
                'project_id' => optional($holder->projects()->first())->id,
                'unit_id' => $holder->unit_id,

                'month' => $data['month'],
                'year' => $data['year'],

                'total_hours' => $totalHours,
                'amount' => $amount,

                'status' => Payment::STATUS_SENT,
                'sent_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.payments.create')
            ->with('success', 'Pagamento enviado para execução financeira.');
    }

    public function confirm(Payment $payment)
    {
        $this->authorize('confirm', $payment);

        // se ainda não tiver número de recibo, gerar
        if (!$payment->receipt_number) {
            $payment->receipt_number = Payment::generateReceiptNumber();
        }

        $payment->update([
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'receipt_generated_at' => now(),
        ]);

        return back()->with('success', 'Recebimento confirmado e recibo gerado.');
    }

}
