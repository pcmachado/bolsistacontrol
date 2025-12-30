<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentExecutionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $status = $request->get('status', Payment::STATUS_SENT);

        $payments = Payment::with(['scholarshipHolder.user', 'project', 'unit'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('admin.payments.index', compact('payments', 'status'));
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
                message: "Seu pagamento referente a {$payment->periodLabel()} foi realizado. Confirme o recebimento assim que possível.",
                level: 'success',
                url: route('payments.my')
            )
        );

        return back()->with('success', 'Pagamento marcado como pago.');
    }

    public function batchForm()
    {
        $this->authorize('create', Payment::class);

        return view('admin.payments.batch.form', [
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function batchPreview(Request $request)
    {
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000|max:2100',
        ]);

        $unit = Unit::findOrFail($data['unit_id']);

        $records = AttendanceRecord::approved()
            ->whereMonth('date', $data['month'])
            ->whereYear('date', $data['year'])
            ->whereHas('scholarshipHolder', fn ($q) =>
                $q->where('unit_id', $unit->id)
            )
            ->with(['scholarshipHolder.user', 'scholarshipHolder.projects'])
            ->get();

        $grouped = $records->groupBy('scholarship_holder_id');

        $preview = $grouped->map(function ($records) {
            $holder = $records->first()->scholarshipHolder;

            $totalHours = $records->sum('hours');
            $amount     = $records->sum('calculated_value');

            return [
                'holder'      => $holder,
                'total_hours'=> $totalHours,
                'amount'     => $amount,
            ];
        })->filter(fn ($row) => $row['total_hours'] > 0);

        return view('admin.payments.batch.preview', [
            'unit'    => $unit,
            'month'   => $data['month'],
            'year'    => $data['year'],
            'preview' => $preview,
        ]);
    }

    public function batchStore(Request $request)
    {
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'month'   => 'required|integer',
            'year'    => 'required|integer',
        ]);

        DB::transaction(function () use ($data) {

            $records = AttendanceRecord::approved()
                ->whereMonth('date', $data['month'])
                ->whereYear('date', $data['year'])
                ->whereHas('scholarshipHolder', fn ($q) =>
                    $q->where('unit_id', $data['unit_id'])
                )
                ->get()
                ->groupBy('scholarship_holder_id');

            foreach ($records as $holderId => $items) {

                // evita duplicidade
                if (
                    Payment::where('scholarship_holder_id', $holderId)
                        ->where('month', $data['month'])
                        ->where('year', $data['year'])
                        ->exists()
                ) {
                    continue;
                }

                Payment::create([
                    'scholarship_holder_id' => $holderId,
                    'unit_id'               => $data['unit_id'],
                    'project_id'            => optional(
                        ScholarshipHolder::find($holderId)->projects()->first()
                    )->id,
                    'month'       => $data['month'],
                    'year'        => $data['year'],
                    'total_hours' => $items->sum('hours'),
                    'amount'      => $items->sum('calculated_value'),
                    'status'      => Payment::STATUS_SENT,
                    'sent_at'     => now(),
                ]);
            }
        });

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pagamentos em lote gerados com sucesso.');
    }

}
