@extends('layouts.app')

@section('title', 'Homologação de Frequência')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-check2-square me-2"></i> Homologação de Frequência
        </h3>

        <span class="text-muted">
            {{ str_pad($submission->month,2,'0',STR_PAD_LEFT) }}/{{ $submission->year }}
        </span>
    </div>

    {{-- INFO DO BOLSISTA --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Bolsista:</strong>
                    {{ $submission->scholarshipHolder->user->name ?? '-' }}
                </div>

                <div>
                    <strong>Status:</strong>
                    <span class="badge bg-{{ match($submission->status) {
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    } }}">
                        {{ ucfirst($submission->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM REJEIÇÃO --}}
    <form method="POST" action="{{ route('admin.homologations.reject', ['submission' => $submission->id]) }}">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                Registros do mês

                <small class="text-muted">
                    {{ $submission->AttendanceRecords->count() }} registros
                </small>
            </div>

            <div class="card-body p-0">

                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="check-all">
                            </th>
                            <th>Data</th>
                            <th>Horas</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($submission->AttendanceRecords as $record)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           name="records[]"
                                           value="{{ $record->id }}"
                                           class="record-checkbox"
                                           checked>
                                </td>

                                <td>{{ $record->date->format('d/m/Y') }}</td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $record->formattedDuration() }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ match($record->computed_status) {
                                        'draft' => 'secondary',
                                        'submitted' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'light'
                                    } }}">
                                        {{ $record->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Nenhum registro encontrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        {{-- MOTIVO --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-semibold">
                    Motivo da rejeição
                </label>

                <textarea name="reason"
                          class="form-control"
                          rows="3"
                          placeholder="Opcional, mas recomendado para orientar o bolsista..."></textarea>
            </div>
        </div>

        {{-- AÇÕES --}}
        <div class="d-flex gap-2 mb-4">

            <button class="btn btn-danger">
                <i class="bi bi-x-circle me-1"></i>
                Rejeitar selecionados
            </button>

        </div>

    </form>

    {{-- APROVAR --}}
    <form method="POST"
          action="{{ route('admin.homologations.approve', $submission) }}">
        @csrf

        <button class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>
            Aprovar mês inteiro
        </button>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // selecionar/desmarcar todos
    document.getElementById('check-all')?.addEventListener('change', function () {
        document.querySelectorAll('.record-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endpush