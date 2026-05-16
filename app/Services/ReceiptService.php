<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\Payment;
use PDF;

class ReceiptService
{
    public function generate(Payment $payment)
    {
        $template = DocumentTemplate::for(
            'payment_receipt',
            $payment->unit_id,
            optional($payment->unit)->institution_id
        );

        if (! $template) {
            throw new \Exception('Template de recibo não configurado.');
        }

        $data = [
            '{{ scholarship_holder }}' => $payment->scholarshipHolder->name,
            '{{ cpf }}' => $payment->scholarshipHolder->cpf,
            '{{ project }}' => $payment->project->name ?? '-',
            '{{ amount }}' => number_format($payment->amount, 2, ',', '.'),
            '{{ period }}' => $payment->periodLabel(),
            '{{ institution }}' => optional($payment->unit->institution)->name ?? '',
            '{{ unit }}' => $payment->unit->name ?? '',
            '{{ generated_at }}' => now()->format('d/m/Y H:i'),
            '{{ logo_url }}' => asset('storage/logo.png'),
        ];

        $html = $template->renderHtml($data);

        return PDF::loadHTML($html)->download(
            "recibo_pagamento_{$payment->id}.pdf"
        );
    }
}
