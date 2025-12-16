@switch($p->status)
    @case('sent_to_payment')
        <span class="badge bg-warning text-dark">Enviado</span>
        @break
    @case('paid')
        <span class="badge bg-info">Pago</span>
        @break
    @case('confirmed')
        <span class="badge bg-success">Confirmado</span>
        @break
    @default
        <span class="badge bg-secondary">{{ $p->status }}</span>
@endswitch
