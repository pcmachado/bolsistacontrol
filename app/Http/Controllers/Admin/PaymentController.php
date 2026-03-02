<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ScholarshipHolder;
use App\Models\AttendanceRecord;
use App\Models\FinancialClosure;
use App\Services\FinancialAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\PaymentDataTable;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request, PaymentDataTable $dataTable)
    {
        $dataTable->setFilters($request->all());
        $dataTable->mode = 'admin';
        
        return $dataTable->render('admin.payments.index', [
            'status' => $request->get('status'),
            'month'  => $request->get('month'),
            'year'   => $request->get('year'),
        ]);
    }

    /**
     * Formulário de geração de pagamento
     */
    public function create()
    {
        return view('admin.payments.create', [
            'scholarshipHolders' => ScholarshipHolder::with('user')->orderBy('id')->get(),
        ]);
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Gera pagamento individual
     */
    public function store(Request $request)
    {
        $fundingSource = $request->input('funding_source_id');
        $amount = $request->input('amount');
        
        if (!$fundingSource->hasBalance($amount)) {
            throw ValidationException::withMessages([
                'funding_source_id' => 'Saldo insuficiente na fonte de fomento.'
            ]);
        }

        $data = $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2100',
        ]);

        $holder = ScholarshipHolder::with(['unit', 'projects'])->findOrFail($data['scholarship_holder_id']);

        // 🔒 Bloqueio por fechamento financeiro
        if (FinancialClosure::isClosed($holder->unit_id, $data['month'], $data['year'])) {
            return back()->withErrors('O período financeiro está fechado.');
        }

        // Evita duplicidade
        $exists = Payment::where([
            'scholarship_holder_id' => $holder->id,
            'month' => $data['month'],
            'year' => $data['year'],
        ])->exists();

        if ($exists) {
            return back()->withErrors('Já existe pagamento para este período.');
        }

        // Frequências homologadas
        $records = AttendanceRecord::approved()
            ->where('scholarship_holder_id', $holder->id)
            ->whereMonth('date', $data['month'])
            ->whereYear('date', $data['year'])
            ->get();

        if ($records->isEmpty()) {
            return back()->withErrors('Nenhuma frequência homologada encontrada.');
        }

        DB::transaction(function () use ($data, $holder, $records) {

            $payment = Payment::create([
                'scholarship_holder_id' => $holder->id,
                'project_id' => optional($holder->projects()->first())->id,
                'unit_id' => $holder->unit_id,
                'month' => $data['month'],
                'year' => $data['year'],
                'total_hours' => $records->sum('hours'),
                'amount' => $records->sum('calculated_value'),
                'status' => Payment::STATUS_SENT,
                'sent_at' => now(),
            ]);

            FinancialAuditService::log(
                'created',
                'Payment',
                $payment->id,
                ['amount' => $payment->amount]
            );
        });

        return redirect()
            ->route('admin.payments.create')
            ->with('success', 'Pagamento enviado para execução financeira.');
    }

    /**
     * Marca pagamento como pago
     */
    public function pay(Payment $payment)
    {
        if (! $payment->canBePaid()) {
            abort(403);
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            FinancialAuditService::log(
                'paid',
                'Payment',
                $payment->id,
                ['amount' => $payment->amount]
            );
        });

        return redirect()
            ->back()
            ->with('success', 'Pagamento marcado como pago.');
    }

}
