@extends('layouts.app')

@section('title', 'Cadastrar Novo Bolsista')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Cadastrar Novo Bolsista</h1>
        <a href="{{ route('bolsistas.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <!-- Alertas de Erro -->
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

    <!-- Alerta de Automação (Usuário vinculado) -->
    @if(isset($userPreenchido))
        <div class="alert alert-info shadow-sm d-flex align-items-center" role="alert">
            <i class="bi bi-person-check-fill fs-4 me-3"></i>
            <div>
                <strong>Atenção:</strong> Criando cadastro de bolsista vinculado ao usuário recém-criado <strong>{{ $userPreenchido->name }}</strong>.
            </div>
        </div>
    @endif

    <!-- Formulário -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('bolsistas.store') }}" method="POST">
                @csrf

                <!-- Input Oculto do ID do Usuário (se existir) -->
                @if(isset($userPreenchido))
                    <input type="hidden" name="user_id" value="{{ $userPreenchido->id }}">
                @endif

                <div class="row g-4">

                    <!-- SESSÃO: Dados Pessoais -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-person me-2"></i>Dados Pessoais
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="nome" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" value="{{ old('nome', $userPreenchido->name ?? '') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $userPreenchido->email ?? '') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="cpf" class="form-label fw-bold">CPF <span class="text-danger">*</span></label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" class="form-control" required placeholder="000.000.000-00">
                    </div>

                    <div class="col-md-6">
                        <label for="telefone" class="form-label fw-bold">Telefone / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" class="form-control" required placeholder="(00) 00000-0000">
                    </div>

                    <!-- SESSÃO: Atuação -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-briefcase me-2"></i>Dados de Atuação
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <label for="cargo_id" class="form-label fw-bold">Cargo <span class="text-danger">*</span></label>
                        <select name="cargo_id" id="cargo_id" class="form-select" required>
                            <option value="">Selecione um cargo...</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                    {{ $cargo->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="unidade_id" class="form-label fw-bold">Unidade de Atuação <span class="text-danger">*</span></label>
                        <select name="unidade_id" id="unidade_id" class="form-select" required>
                            <option value="">Selecione uma unidade...</option>
                            @foreach($unidades as $unidade)
                                <option value="{{ $unidade->id }}" {{ old('unidade_id') == $unidade->id ? 'selected' : '' }}>
                                    {{ $unidade->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="carga_horaria" class="form-label fw-bold">Carga Horária Mensal <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="carga_horaria" id="carga_horaria" value="{{ old('carga_horaria') }}" class="form-control" min="1" required>
                            <span class="input-group-text bg-light text-muted">horas</span>
                        </div>
                    </div>

                    <!-- SESSÃO: Dados Bancários -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-bank me-2"></i>Dados Bancários (Opcional)
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <label for="banco" class="form-label fw-bold">Banco</label>
                        <input type="text" name="banco" id="banco" value="{{ old('banco') }}" class="form-control" placeholder="Ex: Banco do Brasil">
                    </div>

                    <div class="col-md-4">
                        <label for="agencia" class="form-label fw-bold">Agência</label>
                        <input type="text" name="agencia" id="agencia" value="{{ old('agencia') }}" class="form-control" placeholder="Ex: 1234-5">
                    </div>

                    <div class="col-md-4">
                        <label for="conta" class="form-label fw-bold">Conta</label>
                        <input type="text" name="conta" id="conta" value="{{ old('conta') }}" class="form-control" placeholder="Corrente ou Poupança">
                    </div>

                    <!-- Botões de Ação -->
                    <div class="col-12 text-end mt-5">
                        <hr class="mb-4">
                        <a href="{{ route('bolsistas.index') }}" class="btn btn-light border shadow-sm me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-save me-1"></i> Cadastrar Bolsista
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
