<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PaymentDataTable;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\FinancialClosure;
use App\Models\Payment;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Services\FinancialAuditService;
use App\Services\NotificationService;
use App\Services\PaymentService;
use App\Support\Traits\PaymentFilters;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected $paymentService;

    use PaymentFilters;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request, PaymentDataTable $dataTable)
    {
        $user = Auth::user();

        $summary = $this->paymentService->summary($user, $request);

        $dataTable->mode = 'admin';

        $monthString = $request->get('month', now()->format('Y-m'));

        if (str_contains($monthString, '-')) {
            [$year, $monthNumber] = explode('-', $monthString);
        } else {
            $year = $request->get('year', now()->year);
            $monthNumber = $monthString;
        }

        return $dataTable->render('admin.payments.index', [
            'units' => Unit::all(),
            'projects' => Project::all(),
            'positions' => Position::all(),
            'status' => $request->get('status'),
            'month' => (int) $monthNumber,
            'year' => (int) $year,
            'monthString' => $monthString,
            ...$summary,
        ]);
    }

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'funding_source_id' => 'required|exists:funding_sources,id',
        ]);

        $fundingSource = \App\Models\FundingSource::findOrFail($data['funding_source_id']);

        $holder = ScholarshipHolder::with(['unit', 'projects'])->findOrFail($data['scholarship_holder_id']);

        // 🔒 fechamento financeiro
        if (FinancialClosure::isClosed($holder->unit_id, $data['month'], $data['year'])) {
            return back()->withErrors('O período financeiro está fechado.');
        }

        // duplicidade
        $exists = Payment::where([
            'scholarship_holder_id' => $holder->id,
            'month' => $data['month'],
            'year' => $data['year'],
        ])->exists();

        if ($exists) {
            return back()->withErrors('Já existe pagamento para este período.');
        }

        // frequências homologadas
        $records = AttendanceRecord::approved()
            ->where('scholarship_holder_id', $holder->id)
            ->whereMonth('date', $data['month'])
            ->whereYear('date', $data['year'])
            ->get();

        if ($records->isEmpty()) {
            return back()->withErrors('Nenhuma frequência homologada encontrada.');
        }

        $amount = $records->sum('calculated_value');

        if (! $fundingSource->hasBalance($amount)) {
            throw ValidationException::withMessages([
                'funding_source_id' => 'Saldo insuficiente na fonte de fomento.',
            ]);
        }

        $payment = DB::transaction(function () use ($data, $holder, $records, $amount) {

            $payment = Payment::create([
                'scholarship_holder_id' => $holder->id,
                'project_id' => optional($holder->projects()->first())->id,
                'unit_id' => $holder->unit_id,
                'month' => $data['month'],
                'year' => $data['year'],
                'total_hours' => $records->sum('hours'),
                'amount' => $amount,
                'status' => Payment::STATUS_SENT,
                'sent_at' => now(),
            ]);

            FinancialAuditService::log(
                'created',
                'Payment',
                $payment->id,
                ['amount' => $payment->amount]
            );

            return $payment;
        });

        $payment->loadMissing(['scholarshipHolder.user', 'unit']);

        app(NotificationService::class)->sendEventNotification(
            'payment_sent_to_financial',
            [
                'title' => 'Pagamento enviado ao financeiro',
                'message' => "O pagamento de {$payment->scholarshipHolder->user->name} para {$payment->periodLabel()} foi enviado para execução.",
                'level' => 'warning',
                'payment_id' => $payment->id,
                'new_status' => Payment::STATUS_SENT,
                'url' => route('admin.payments.show', $payment),
                'scholarship_holder_name' => $payment->scholarshipHolder->user->name,
                'period' => $payment->periodLabel(),
                'amount' => number_format($payment->amount, 2, ',', '.'),
            ],
            $payment->project_id,
            $payment->unit?->institution_id
        );

        return redirect()
            ->route('admin.payments.create')
            ->with('success', 'Pagamento enviado para execução financeira.');
    }

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

        return back()->with('success', 'Pagamento marcado como pago.');
    }

    public function pdf(Unit $unit, Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $payments = $this->paymentService->monthly($unit, $month, $year);

        return Pdf::loadView('payments.pdf', [
            'payments' => $payments,
            'unit' => $unit,
            'month' => $month,
            'year' => $year,
            'isPdf' => true,
        ])->download("pagamentos_{$month}_{$year}.pdf");
    }

    public function reportMonthly(Request $request)
    {
        $user = Auth::user();

        $query = Payment::with([
            'unit',
            'project',
            'scholarshipHolder.user',
            'scholarshipHolder.projects',
        ]);

        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, $user, 'admin');

        // 🔥 usar trait centralizado
        $query = $this->applyPaymentFilters($query, $request);

        $payments = $query->get();

        $grouped = $payments->groupBy('unit_id')->map(function ($items) {
            return [
                'unit' => $items->first()->unit?->name,
                'total' => $items->sum('amount'),
                'payments' => $items->map(function ($p) {
                    return [
                        'holder' => $p->scholarshipHolder?->user?->name,
                        'project' => $p->project?->name,
                        'amount' => $p->amount,
                        'status' => $p->status,
                        'period' => str_pad($p->month, 2, '0', STR_PAD_LEFT).'/'.$p->year,
                    ];
                }),
            ];
        });

        $totalGeral = $payments->sum('amount');

        $isPdf = $request->boolean('pdf');

        if ($isPdf) {
            return Pdf::loadView(
                'admin.payments.reports.monthly',
                compact('grouped', 'totalGeral', 'isPdf')
            )->stream('relatorio_consolidado.pdf');
        }

        return view('admin.payments.reports.monthly', compact('grouped', 'totalGeral', 'isPdf'));
    }
}
