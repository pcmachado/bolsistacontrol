<table>
    <thead>
        <tr>
            @if(!$unit)
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
            <th>Valor Hora (R$)</th>
            <th>Total (R$)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $item)
        <tr>
            @if(!$unit)
                <td>{{ $item['unit'] ?? '-' }}</td>
            @endif
            <td>{{ $item['scholarshipHolder'] }}</td>
            <td>{{ $item['phone'] ?? '-' }}</td>
            <td>{{ $item['cpf'] ?? '-' }}</td>
            <td>{{ $item['bank'] ?? '-' }}</td>
            <td>{{ $item['agency'] ?? '-' }}</td>
            <td>{{ $item['account'] ?? '-' }}</td>
            <td>{{ $item['expected_hours'] ?? '-' }}</td>
            <td>{{ $item['totalHours'] }}</td>
            <td>{{ number_format($item['hourlyRate'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['totalValue'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="{{ $unit ? 9 : 10 }}" style="text-align: right;"><strong>Total</strong></td>
            <td><strong>R$ {{ number_format($report->sum('totalValue'), 2, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>

<table>
    <tr>
        <td colspan="{{ $unit ? 10 : 11 }}" style="text-align: right; font-size: 11px; padding-top: 20px;">
            Emitido por {{ Auth::user()->name }} em {{ now()->format('d/m/Y H:i') }}
        </td>
    </tr>
</table>
