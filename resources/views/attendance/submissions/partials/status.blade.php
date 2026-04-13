@php
$colors = [
    'draft'     => 'secondary',
    'submitted' => 'info',
    'approved'  => 'success',
    'rejected'  => 'danger',
];
@endphp

<span class="badge bg-{{ $colors[$row->status] ?? 'secondary' }}">
    {{ ucfirst($row->status) }}
</span>
