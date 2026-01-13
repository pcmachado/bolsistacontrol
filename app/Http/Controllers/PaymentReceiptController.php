<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\View\View;

class PaymentReceiptController extends Controller
{
    public function download(Payment $payment, ReceiptService $service)
    {
        $this->authorize('view', $payment);

        if (! $payment->isConfirmed()) {
            abort(403, 'Recibo disponível somente após confirmação.');
        }

        return $service->generate($payment);
    }
}

