<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Autorização de Pagamento - {{ $month }}/{{ $year }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        .header { text-align: center; font-weight: bold; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { border: none; text-align: center; padding: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <p>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA<br>
        Pró-Reitoria de Extensão - PROEX</p>
        <p><strong>Autorização de Pagamento de Bolsistas - Campus {{ Auth::user()->unit->name ?? 'XXXXX' }}</strong></p>
        <p>Mês: {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if(!$unitId)
                    <th>Unidade</th>
                @endif
                <th>Nome</th>
                <th>Telefone</th>
                <th>CPF</th>
                <th>Banco</th>
                <th>Agência</th>
                <th>Conta</th>
                <th>Horas Previstas</th>
                <th>Horas Cumpridas</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $item)
            <tr>
                @if(!$unitId)
                    <td>{{ $item->scholarshipHolder->units->first()->name ?? '-' }}</td>
                @endif
                <td>{{ $item->scholarshipHolder->name }}</td>
                <td>{{ $item->scholarshipHolder->phone }}</td>
                <td>{{ $item->scholarshipHolder->cpf }}</td>
                <td>{{ $item->scholarshipHolder->bank }}</td>
                <td>{{ $item->scholarshipHolder->agency }}</td>
                <td>{{ $item->scholarshipHolder->account }}</td>
                <td>
                    @if($item->scholarshipHolder->weekly_limit_minutes)
                        {{ ($item->scholarshipHolder->weekly_limit_minutes / 60) * 4 }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->total_hours }}</td>
                <td>R$ {{ number_format($item->total_value, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="{{ $unitId ? 9 : 10 }}" style="text-align: right;"><strong>Total</strong></td>
                <td><strong>R$ {{ number_format($report->sum('total_value'), 2, ',', '.') }}</strong></td>
            </tr>
</tbody>

    </table>

    <table class="signatures">
        <tr>
            <td>_________________________________<br>Coordenação Adjunta</td>
            <td>_________________________________<br>Coordenação Geral</td>
        </tr>
    </table>

    <p style="margin-top: 40px; font-size: 11px; text-align: right;">
        Emitido por {{ Auth::user()->name }} em {{ now()->format('d/m/Y H:i') }}
    </p>

</body>
</html>
