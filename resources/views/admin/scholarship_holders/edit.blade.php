@extends('layouts.app')

@section('title', 'Editar Bolsista')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Editar Bolsista</h1>
        <a href="{{ route('admin.scholarship_holders.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Erro!</strong> Há problemas com os dados inseridos.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.scholarship_holders.update', $scholarshipHolder) }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $scholarshipHolder->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $scholarshipHolder->email) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="cpf" class="form-label fw-bold">CPF <span class="text-danger">*</span></label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $scholarshipHolder->cpf) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold">Telefone / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $scholarshipHolder->phone) }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label for="unit_id" class="form-label fw-bold">Unidade de Atuação <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-select" required>
                            <option value="">Selecione uma unidade...</option>
                            @foreach($units as $id => $name)
                                <option value="{{ $id }}" {{ old('unit_id', $scholarshipHolder->unit_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status', $scholarshipHolder->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ old('status', $scholarshipHolder->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="role" class="form-label fw-bold">Função <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">Selecione a função</option>
                            @foreach($roles as $id => $name)
                                <option value="{{ $id }}" {{ old('role', $user?->roles->pluck('name')->first()) == $id ? 'selected' : '' }}>
                                    {{ ucfirst($name) }}
                                </option>
                            @endforeach
                        </select>  
                    </div>

                    @if($scholarshipHolder->projects->isNotEmpty())
                        <div class="col-12 mt-3">
                            <h5 class="border-bottom pb-2 mb-0 text-primary">
                                <i class="bi bi-briefcase me-2"></i>Cargos por Projeto
                            </h5>
                        </div>

                        @foreach($scholarshipHolder->projects as $project)
                            <div class="col-md-6">
                                <label for="project_position_{{ $project->id }}" class="form-label fw-bold">
                                    {{ $project->name }}
                                </label>
                                <select
                                    name="positions_by_project[{{ $project->id }}]"
                                    id="project_position_{{ $project->id }}"
                                    class="form-select">
                                    <option value="">Selecione o cargo...</option>
                                    @foreach($project->positions as $position)
                                        <option
                                            value="{{ $position->id }}"
                                            {{ old("positions_by_project.{$project->id}", $project->pivot->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    @endif

                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-bold">Data de Início <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', optional($scholarshipHolder->start_date)->format('Y-m-d') ?? $scholarshipHolder->start_date) }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-bold">Data de Término</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', optional($scholarshipHolder->end_date)->format('Y-m-d') ?? $scholarshipHolder->end_date) }}" class="form-control">
                    </div>

                    <div class="col-12 mt-3">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-bank me-2"></i>Dados Bancários (Opcional)
                        </h5>
                    </div>

                    <div class="col-md-3">
                        <label for="bank" class="form-label fw-bold">Banco</label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank', $scholarshipHolder->bank) }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="agency" class="form-label fw-bold">Agência</label>
                        <input type="text" name="agency" id="agency" value="{{ old('agency', $scholarshipHolder->agency) }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="account" class="form-label fw-bold">Conta</label>
                        <input type="text" name="account" id="account" value="{{ old('account', $scholarshipHolder->account) }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="pix_key" class="form-label fw-bold">Chave PIX</label>
                        <input type="text" name="pix_key" id="pix_key" value="{{ old('pix_key', $scholarshipHolder->pix_key) }}" class="form-control">
                    </div>

                    <div class="col-12 text-end mt-4">
                        <hr class="mb-4">
                        <a href="{{ route('admin.scholarship_holders.index') }}" class="btn btn-light border shadow-sm me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-save me-1"></i> Salvar alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
