@extends('layouts.app')

@section('title', 'Editar Registro de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="bi bi-pencil-square me-2"></i> Editar Registro de Frequência</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('attendance.update', $attendanceRecord->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="date" class="form-label">Data</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ old('date', $attendanceRecord->date->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="hours" class="form-label">Horas Trabalhadas</label>
                    <input type="number" name="hours" id="hours" class="form-control" min="1" max="12" 
                           value="{{ old('hours', $attendanceRecord->hours) }}" required>
                </div>

                <div class="mb-3">
                    <label for="observation" class="form-label">Atividades / Observações</label>
                    <textarea name="observation" id="observation" rows="3" class="form-control">{{ old('observation', $attendanceRecord->observation) }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
