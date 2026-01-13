<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .box {
            border: 1px solid #000;
            padding: 15px;
        }
        .line {
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>RECIBO DE PAGAMENTO</h2>
</div>

<div class="box">
    <div class="line"><strong>Bolsista:</strong> {{ $payment->scholarshipHolder->user->name }}</div>
    <div class="line"><strong>CPF:</strong> {{ $payment->scholarshipHolder->cpf }}</div>
    <div class="line"><strong>Projeto:</strong> {{ $payment->project?->name }}</div>
    <div class="line"><strong>Unidade:</strong> {{ $payment->unit?->name }}</div>

    <hr>

    <div class="line"><strong>Período:</strong> {{ $payment->periodLabel() }}</div>
    <div class="line"><strong>Total de horas:</strong> {{ $payment->total_hours }}</div>
    <div class="line"><strong>Valor pago:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</div>

    <hr>

    <div class="line"><strong>Data do pagamento:</strong> {{ optional($payment->paid_at)->format('d/m/Y') }}</div>
    <div class="line"><strong>Confirmado em:</strong> {{ optional($payment->confirmed_at)->format('d/m/Y') }}</div>
</div>

<div class="footer">
    <p>
        Declaro que recebi o valor acima referente às atividades desenvolvidas no período informado.
    </p>

    <br><br>

    _______________________________________<br>
    {{ $payment->scholarshipHolder->user->name }}
</div>

<hr>

<p style="font-size: 10px; word-break: break-all;">
    <strong>Código de verificação:</strong><br>
    {{ $payment->receipt_hash }}
</p>

<p style="font-size: 10px;">
    Este recibo pode ser verificado em:<br>
    {{ url('verificar-recibo') }}
</p>


</body>
</html>
