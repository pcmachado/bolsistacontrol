<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialClosure;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialClosureController extends Controller
{
    public function index(Request $request)
    {
        $monthString = $request->get('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $monthString);

        $query = FinancialClosure::with(['unit', 'closedBy']);

        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, Auth::user(), 'admin');

        $closures = $query
            ->when($request->filled('month'), function ($q) use ($month, $year) {
                $q->where('month', $month)
                    ->where('year', $year);
            })
            ->latest()
            ->get();

        return view('admin.financial-closures.index', [
            'closures' => $closures,
            'units' => Unit::all(),
            'monthString' => $monthString,
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'month' => 'required',
        ]);

        [$year, $month] = explode('-', $request->month);

        $query = \App\Models\Payment::with([
            'unit',
            'project',
            'scholarshipHolder.user',
        ])
            ->where('month', $month)
            ->where('year', $year);

        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, Auth::user(), 'admin');

        $payments = $query->get()->groupBy('unit_id');

        return view('admin.financial-closures.preview', compact(
            'payments',
            'month',
            'year'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        if (Auth::user()->hasRole('coordenador_adjunto')) {
            abort_unless(Auth::user()->unit_id == $data['unit_id'], 403);
        }

        if (FinancialClosure::isClosed(
            $data['unit_id'],
            $data['month'],
            $data['year']
        )) {
            return back()->withErrors('Este período já está fechado.');
        }

        FinancialClosure::create([
            'unit_id' => $data['unit_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'closed_at' => now(),
            'closed_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Fechamento financeiro realizado.');
    }

    public function destroy(FinancialClosure $closure)
    {
        $closure->delete();

        return back()->with('success', 'Fechamento removido.');
    }

    public function show(FinancialClosure $financialClosure)
    {
        $payments = \App\Models\Payment::with([
            'unit',
            'project',
            'scholarshipHolder.user',
        ])
            ->where('month', $financialClosure->month)
            ->where('year', $financialClosure->year)
            ->where('unit_id', $financialClosure->unit_id)
            ->get();

        return view('admin.financial-closures.show', compact(
            'financialClosure',
            'payments'
        ));
    }
}
