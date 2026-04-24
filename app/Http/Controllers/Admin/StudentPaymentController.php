<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\StudentPaymentDataTable;
use App\Http\Controllers\Controller;
use App\Services\StudentPaymentDashboardService;
use App\Services\PaymentMonthService;
use App\Models\StudentPayment;
use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentPaymentExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentPaymentController extends Controller
{
    /**
     * 📋 LISTAGEM
     */
    public function index(Request $request, StudentPaymentDataTable $dataTable, PaymentMonthService $paymentMonthService)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $monthsData = $paymentMonthService->getMonths($year);

        $current = \Carbon\Carbon::create($year, $month, 1);

        $prev = $current->copy()->subMonth();
        $next = $current->copy()->addMonth();

        $filters = $request->only([
            'month',
            'year',
            'status',
            'class_id',
            'unit_id',
            'course_id'
        ]);

        return $dataTable
            ->setFilters($filters)
            ->render('admin.student-payments.index',[
                'filters' => $filters,
            ],
            compact(
                'monthsData',
                'month',
                'year',
                'prev',
                'next',
                'current'
        ));
    }

    /**
     * 💰 MARCAR COMO PAGO
     */
    public function pay(StudentPayment $payment)
    {
        if ($payment->status === StudentPayment::STATUS_PAID) {
            return back()->with('info', 'Pagamento já foi realizado.');
        }

        $payment->markAsPaid(Auth::id());

        return back()->with('success', 'Pagamento realizado com sucesso.');
    }

    /**
     * 💰 PAGAMENTO EM LOTE
     */
    public function payBatch(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('warning', 'Nenhum pagamento selecionado.');
        }

        DB::transaction(function () use ($ids) {

            $payments = StudentPayment::whereIn('id', $ids)
                ->where('status', '!=', StudentPayment::STATUS_PAID)
                ->get();

            foreach ($payments as $payment) {
                $payment->markAsPaid(Auth::id());
            }
        });

        return back()->with('success', 'Pagamentos realizados em lote.');
    }

    public function dashboard(Request $request, StudentPaymentDashboardService $service, StudentPaymentDataTable $dataTable) 
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $monthsData = app(PaymentMonthService::class)->getMonths($year);

        $current = \Carbon\Carbon::create($year, $month, 1);

        $prev = $current->copy()->subMonth();
        $next = $current->copy()->addMonth();

        $filters = [
            'month' => $request->month ?? now()->month,
            'year'  => $request->year ?? now()->year,
            'status' => $request->status,
            'unit_id' => $request->unit_id,
            'course_id' => $request->course_id,
        ];

        $data = $service->data($filters);

        return $dataTable
            ->setFilters($filters)
            ->render('admin.student-payments.dashboard', $data, compact(
                'monthsData',
                'month',
                'year',
                'prev',
                'next',
                'current'
        ));
    }

    private function buildQuery(array $filters)
    {
        $query = StudentPayment::with([
            'student',
            'classOffering.unit',
            'classOffering.course'
        ]);

        if (!empty($filters['month'])) {
            $query->where('month', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['unit_id'])) {
            $query->whereHas('classOffering', fn($q) =>
                $q->where('unit_id', $filters['unit_id'])
            );
        }

        if (!empty($filters['course_id'])) {
            $query->whereHas('classOffering', fn($q) =>
                $q->where('course_id', $filters['course_id'])
            );
        }

        return $query;
    }
    
    public function reportPdf(Request $request)
    {
        $filters = $request->all();

        if (empty($filters['unit_id'])) {
            return back()->with('warning', 'Selecione ao menos Unidade.');
        }

        $payments = $this->buildQuery($filters)->get();

        $total = $payments->sum('amount');

        $pdf = Pdf::loadView(
            'admin.student-payments.report-pdf',
            [
                'payments' => $payments,
                'total' => $total,
                'isPdf' => true // 🔥 ESSENCIAL
            ]
        );

        return $pdf->download('pagamentos-alunos.pdf');
    }

    public function reportExcel(Request $request)
    {
        $filters = $request->all();

        if (empty($filters['unit_id'])) {
            return back()->with('warning', 'Selecione ao menos Unidade.');
        }

        $payments = $this->buildQuery($filters)->get();

        return Excel::download(
            new StudentPaymentExport($payments),
            'pagamentos-alunos.xlsx'
        );
    }

    public function closeMonth(ClassOffering $offering, $month, $year)
    {
        ClassOfferingSubmission::updateOrCreate([
            'class_offering_id' => $offering->id,
            'month' => substr($month, 5, 2),
            'year' => substr($month, 0, 4),
        ], [
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // 🔥 recalcula tudo
        app(\App\Services\StudentRecordService::class)->generate($offering);

        return back()->with('success', 'Mês fechado com sucesso');
    }
}