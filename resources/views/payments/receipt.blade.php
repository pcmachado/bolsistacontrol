@extends('layouts.pdf')

@section('title', 'Recibo de Pagamento')

@section('header-extra')
    <h4>RECIBO DE PAGAMENTO</h4>
@endsection

@section('content')

<table class="no-break">
    <tr>
        <td><strong>Bolsista</strong></td>
        <td>{{ $payment->scholarshipHolder->user->name }}</td>
    </tr>
    <tr>
        <td><strong>CPF</strong></td>
        <td>{{ $payment->scholarshipHolder->cpf }}</td>
    </tr>
    <tr>
        <td><strong>Projeto</strong></td>
        <td>{{ $payment->project?->name }}</td>
    </tr>
    <tr>
        <td><strong>Unidade</strong></td>
        <td>{{ $payment->unit?->name }}</td>
    </tr>
</table>

<table class="no-break">
    <tr>
        <td><strong>Período</strong></td>
        <td>{{ $payment->periodLabel() }}</td>
    </tr>
    <tr>
        <td><strong>Total de horas</strong></td>
        <td>{{ $payment->total_hours }}</td>
    </tr>
    <tr>
        <td><strong>Valor pago</strong></td>
        <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
    </tr>
</table>

<table class="no-break">
    <tr>
        <td><strong>Data do pagamento</strong></td>
        <td>{{ optional($payment->paid_at)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td><strong>Confirmado em</strong></td>
        <td>{{ optional($payment->confirmed_at)->format('d/m/Y') }}</td>
    </tr>
</table>

<br><br>

<p>
Declaro que recebi o valor acima referente às atividades desenvolvidas no período informado.
</p>

<table class="assinaturas">
    <tr>
        <td>
            <div class="assinatura-linha"></div>
            {{ $payment->scholarshipHolder->user->name }}
        </td>
    </tr>
</table>

<br>

<p style="font-size:10px;">
<strong>Código de verificação:</strong><br>
{{ $payment->receipt_hash }}
</p>

<p style="font-size:10px;">
Este recibo pode ser verificado em:<br>
{{ route('payments.verify.form') }}
</p>

@endsection