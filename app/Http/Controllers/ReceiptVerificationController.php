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
            'receipt_hash' => 'required|string|size:64',
        ]);

        $payment = Payment::where('receipt_hash', $data['receipt_hash'])
            ->where('status', Payment::STATUS_CONFIRMED)
            ->first();

        if (!$payment) {
            return back()->withErrors([
                'receipt_hash' => 'Recibo inválido ou não encontrado.',
            ]);
        }

        return view('payments.verify_result', compact('payment'));
    }
}
