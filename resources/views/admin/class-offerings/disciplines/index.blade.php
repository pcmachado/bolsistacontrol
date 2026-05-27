@extends('layouts.app')

@section('title', 'Disciplinas da Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-diagram-3 me-2"></i> Disciplinas da Turma: {{ $offering->name ?? 'Sem nome' }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Alerts --}}
    @foreach(['success', 'warning', 'error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} shadow-sm">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach


    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Professores com disciplina vinculada</div>
                <div class="fs-3 fw-bold">{{ $recordedTeachers }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Lançamentos de frequência já registrados</div>
                <div class="fs-3 fw-bold">{{ (int) ($recordsSummary->total_records ?? 0) }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Valor projetado para pagamento (R$)</div>
                <div class="fs-3 fw-bold">{{ number_format((float) ($recordsSummary->projected_total ?? 0), 2, ',', '.') }}</div>
            </div></div>
        </div>
    </div>

    {{-- Vínculo --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle me-2"></i> Adicionar Disciplina à Turma
        </div>

        <div class="card-body">
            <form action="{{ route('admin.class-offerings.disciplines.store', $offering->id) }}" method="POST">
                @csrf

                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Disciplina</label>
                        <select name="discipline_id" class="form-select @error('discipline_id') is-invalid @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($disciplines as $disc)
                                <option value="{{ $disc->id }}" data-workload="{{ (int) ($disc->workload ?? 0) }}" @selected(old('discipline_id') == $disc->id)>{{ $disc->name }}</option>
                            @endforeach
                        </select>
                        @error('discipline_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Professor (opcional)</label>
                        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                            <option value="">Nenhum</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Carga horária da disciplina</label>
                        <input type="number" min="1" name="workload" id="discipline_workload" class="form-control" value="{{ old('workload') }}" readonly>
                        <small class="text-muted">Definida automaticamente pela disciplina selecionada.</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Horário (opcional)</label>
                        <input type="text" name="schedule" class="form-control @error('schedule') is-invalid @enderror" value="{{ old('schedule') }}">
                        @error('schedule') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sala (opcional)</label>
                        <input type="text" name="room" class="form-control @error('room') is-invalid @enderror" value="{{ old('room') }}">
                        @error('room') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i> Adicionar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- Listagem --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-journal-text me-2"></i> Disciplinas Vinculadas
        </div>

        <div class="card-body p-0">
            @include('admin.class-offerings.disciplines.partials.list', [
                'offering' => $offering,
                'teachers' => $teachers
            ])
        </div>
    </div>


    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-exclamation-triangle me-2"></i> Disciplinas sem professor vinculado
        </div>
        <div class="card-body">
            @if($pendingTeachers->isEmpty())
                <div class="alert alert-success mb-0">Todas as disciplinas da turma já possuem professor vinculado.</div>
            @else
                <ul class="mb-0">
                    @foreach($pendingTeachers as $pending)
                        <li>{{ $pending->discipline->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (() => {
        const select = document.querySelector('select[name="discipline_id"]');
        const workload = document.getElementById('discipline_workload');

        const syncWorkload = () => {
            const option = select?.selectedOptions?.[0];
            const value = Number(option?.dataset?.workload || 0);
            workload.value = value > 0 ? value : '';
        };

        select?.addEventListener('change', syncWorkload);
        syncWorkload();
    })();
</script>
@endpush
