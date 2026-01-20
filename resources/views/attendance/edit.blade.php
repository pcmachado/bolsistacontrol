@extends('layouts.app')

@section('title', 'Editar Registro de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="bi bi-pencil-square me-2"></i> Editar Registro de Frequência</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('attendance.update', $attendanceRecord) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="date" class="form-label">Data</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ old('date', $attendanceRecord->date->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="start_time" class="form-label">Hora de Início</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        value="{{ old('start_time', \Carbon\Carbon::parse($attendanceRecord->start_time)->format('H:i')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="end_time" class="form-label">Hora de Término</label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                        value="{{ old('end_time', \Carbon\Carbon::parse($attendanceRecord->end_time)->format('H:i')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Atividades / Observações</label>
                    <textarea name="description" id="description" rows="3" class="form-control sgb-textarea">{{ old('description', $attendanceRecord->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('attendance.my') }}" class="btn btn-secondary">
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
