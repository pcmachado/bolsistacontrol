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
            <td>{{ number_format($item->total_value, 2, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="{{ $unitId ? 9 : 10 }}" style="text-align: right;"><strong>Total</strong></td>
            <td><strong>R$ {{ number_format($report->sum('total_value'), 2, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>

{{-- Rodapé de emissão --}}
<table>
    <tr>
        <td colspan="{{ $unitId ? 10 : 11 }}" style="text-align: right; font-size: 11px; padding-top: 20px;">
            Emitido por {{ Auth::user()->name }} em {{ now()->format('d/m/Y H:i') }}
        </td>
    </tr>
</table>
