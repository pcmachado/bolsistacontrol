@extends('layouts.app')

@section('title', 'Cadastrar Novo Bolsista')

@section('content')
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Cadastrar Novo Bolsista</h1>
        <a href="{{ route('admin.scholarship_holders.index') }}" class="btn btn-secondary shadow-sm">
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
            <form action="{{ route('admin.scholarship_holders.store') }}" method="POST">
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
                        <label for="name" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $userPreenchido->name ?? '') }}" class="form-control" required>
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
                        <label for="phone" class="form-label fw-bold">Telefone / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" required placeholder="(00) 00000-0000">
                    </div>

                    <!-- SESSÃO: Atuação -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-briefcase me-2"></i>Dados de Atuação
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="unit_id" class="form-label fw-bold">Unidade de Atuação <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-select" required>
                            <option value="">Selecione uma unidade...</option>
                            @foreach($units as $id => $name)
                                @php
                                    $unitId = is_object($name) ? $name->id : $id;
                                    $unitName = is_object($name) ? $name->name : $name;
                                @endphp
                                <option value="{{ $unitId }}" {{ old('unit_id') == $unitId ? 'selected' : '' }}>
                                    {{ $unitName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="pix_key" class="form-label fw-bold">Chave PIX <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="pix_key" id="pix_key" value="{{ old('pix_key') }}" class="form-control" required>
                        </div>
                    </div>

                    <!-- SESSÃO: Dados Bancários -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-bank me-2"></i>Dados Bancários (Opcional)
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <label for="bank" class="form-label fw-bold">Banco</label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank') }}" class="form-control" placeholder="Ex: Banco do Brasil">
                    </div>

                    <div class="col-md-4">
                        <label for="agency" class="form-label fw-bold">Agência</label>
                        <input type="text" name="agency" id="agency" value="{{ old('agency') }}" class="form-control" placeholder="Ex: 1234-5">
                    </div>

                    <div class="col-md-4">
                        <label for="account" class="form-label fw-bold">Conta</label>
                        <input type="text" name="account" id="account" value="{{ old('account') }}" class="form-control" placeholder="Corrente ou Poupança">
                    </div>

                    <!-- Botões de Ação -->
                    <div class="col-12 text-end mt-5">
                        <hr class="mb-4">
                        <a href="{{ route('admin.scholarship_holders.index') }}" class="btn btn-light border shadow-sm me-2">Cancelar</a>
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
