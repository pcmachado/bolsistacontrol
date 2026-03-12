<table class="table table-striped">

<thead>
<tr>
<th>Bolsista</th>
<th>CPF</th>
<th>Banco</th>
<th>Agência</th>
<th>Conta</th>
<th>Horas</th>
<th>Valor Hora</th>
<th>Total</th>
</tr>
</thead>

<tbody>

@foreach($payments as $row)

<tr>
<td>{{ $row['holder'] }}</td>
<td>{{ $row['cpf'] }}</td>
<td>{{ $row['bank'] }}</td>
<td>{{ $row['agency'] }}</td>
<td>{{ $row['account'] }}</td>
<td>{{ $row['hours'] }}</td>
<td>{{ number_format($row['rate'],2,',','.') }}</td>
<td>{{ number_format($row['total'],2,',','.') }}</td>
</tr>

@endforeach

</tbody>

</table>