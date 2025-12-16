<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .box { margin-top: 20px; }
    </style>
</head>
<body>

<div class="center title">RECIBO DE PAGAMENTO DE BOLSA</div>

<p>Recibo Nº: <strong>{{ $payment->receipt_number }}</strong></p>
<p>Data de emissão: {{ $payment->receipt_generated_at->format('d/m/Y') }}</p>

<hr>

<p>Eu, <strong>{{ $payment->scholarshipHolder->name }}</strong>,
CPF <strong>{{ $payment->scholarshipHolder->cpf }}</strong>,
confirmo que recebi o valor de 
<strong>R$ {{ number_format($payment->amount, 2, ',', '.') }}</strong>
referente às atividades desempenhadas no mês
de <strong>{{ $payment->month }}/{{ $payment->year }}</strong>
no projeto <strong>{{ $payment->project->name }}</strong>,
da unidade <strong>{{ $payment->unit->name }}</strong>.
</p>

<div class="box">
    <p>
        Garibaldi, {{ now()->format('d') }} de {{ now()->translatedFormat('F') }} de {{ now()->format('Y') }}
    </p>
</div>

<br><br>

<div class="center">
    ___________________________________________<br>
    {{ $payment->scholarshipHolder->name }}<br>
    Bolsista
</div>

</body>
</html>
