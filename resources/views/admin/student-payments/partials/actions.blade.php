<div class="btn-group btn-group-sm">

    @if($p->status !== 'paid')
        <form method="POST" action="{{ route('admin.student-payments.pay', $p) }}">
            @csrf
            <button class="btn btn-success">
                <i class="bi bi-cash"></i>
            </button>
        </form>
    @else
        <span class="badge bg-success">Pago</span>
    @endif

</div>