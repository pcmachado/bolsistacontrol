<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialClosure;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialClosureController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer',
        ]);

        if (FinancialClosure::isClosed(
            $data['unit_id'], $data['month'], $data['year']
        )) {
            return back()->withErrors('Este período já está fechado.');
        }

        FinancialClosure::create([
            'unit_id' => $data['unit_id'],
            'month'   => $data['month'],
            'year'    => $data['year'],
            'closed_at' => now(),
            'closed_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Fechamento financeiro realizado.');
    }
}
