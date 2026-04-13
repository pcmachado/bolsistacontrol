<div class="d-flex gap-1">

    {{-- Visualizar --}}
    <a href="{{ route('admin.payments.show', $payment) }}"
       class="btn btn-sm btn-outline-secondary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    {{-- Se enviado para pagamento --}}
    @if($payment->status === 'sent_to_payment')
        <form method="POST"
              action="{{ route('admin.payments.pay', $payment) }}">
            @csrf
            <button class="btn btn-sm btn-outline-success"
                    title="Marcar como Pago">
                <i class="bi bi-cash-coin"></i>
            </button>
        </form>
    @endif

    {{-- Se pago --}}
    @if($payment->status === 'paid')
        <form method="POST"
              action="{{ route('payments.confirm', $payment) }}">
            @csrf
            <button class="btn btn-sm btn-outline-primary"
                    title="Confirmar">
                <i class="bi bi-check-circle"></i>
            </button>
        </form>
    @endif

    {{-- Se confirmado --}}
    @if($payment->status === 'confirmed')
        <a href="{{ route('payments.receipt', $payment) }}"
           class="btn btn-sm btn-outline-dark"
           title="Recibo">
            <i class="bi bi-receipt"></i>
        </a>
    @endif

</div>
