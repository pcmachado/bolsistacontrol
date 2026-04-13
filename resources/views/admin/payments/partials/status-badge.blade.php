@php
    $color = match ($payment->status) {
        \App\Models\Payment::STATUS_DRAFT     => 'secondary',
        \App\Models\Payment::STATUS_SENT      => 'warning',
        \App\Models\Payment::STATUS_PAID      => 'info',
        \App\Models\Payment::STATUS_CONFIRMED => 'success',
        default                               => 'dark',
    };
@endphp

<span class="badge bg-{{ $color }}">
    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
</span>
