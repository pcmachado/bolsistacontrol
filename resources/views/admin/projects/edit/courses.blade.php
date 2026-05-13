@extends('layouts.app')

@section('title', 'Editar Cursos do Projeto')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold">
        Editar Cursos - {{ $project->name }}
    </h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'courses'])
        </div>

        <div class="col-md-9">
            <form method="POST" action="{{ route('admin.projects.edit.courses.update', $project) }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;"></th>
                                <th>Curso</th>
                                <th style="width:120px;">Ativo</th>
                                <th style="width:120px;">Semestre</th>
                                <th style="width:120px;">Ano</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                @php
                                    $pivot = $project->courses->firstWhere('id', $course->id)?->pivot;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input course-check" {{ $pivot ? 'checked' : '' }}>
                                    </td>
                                    <td><strong>{{ $course->name }}</strong></td>
                                    <td>
                                        <select name="courses[{{ $loop->index }}][active]" class="form-select form-select-sm" {{ $pivot ? '' : 'disabled' }}>
                                            <option value="1" {{ ($pivot && $pivot->active) ? 'selected' : '' }}>Sim</option>
                                            <option value="0" {{ ($pivot && ! $pivot->active) ? 'selected' : '' }}>Nao</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="courses[{{ $loop->index }}][semester]" class="form-control form-control-sm" value="{{ $pivot->semester ?? '' }}" {{ $pivot ? '' : 'disabled' }}>
                                    </td>
                                    <td>
                                        <input type="number" name="courses[{{ $loop->index }}][year]" class="form-control form-control-sm" value="{{ $pivot->year ?? '' }}" {{ $pivot ? '' : 'disabled' }}>
                                    </td>
                                    <input type="hidden" name="courses[{{ $loop->index }}][course_id]" value="{{ $course->id }}">
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Salvar alteracoes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.course-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');

        row.querySelectorAll('input, select').forEach(el => {
            if (el.type !== 'hidden') {
                el.disabled = !this.checked;
            }
        });
    });
});
</script>
@endpush
