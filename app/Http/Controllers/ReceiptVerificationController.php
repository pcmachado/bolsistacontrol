<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class ReceiptVerificationController extends Controller
{
    public function form()
    {
        return view('payments.verify');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'hash' => 'required|string',
        ]);

        $payment = Payment::where('receipt_hash', $data['hash'])
            ->where('status', Payment::STATUS_CONFIRMED)
            ->first();

        if (! $payment) {
            return back()->withErrors([
                'hash' => 'Recibo inválido ou não encontrado.',
            ]);
        }

        $searched = true;

        return view('payments.verify_result', compact('payment', 'searched'));
    }
}
