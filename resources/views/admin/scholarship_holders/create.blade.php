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
    @if(isset($user))
        <div class="alert alert-info shadow-sm d-flex align-items-center" role="alert">
            <i class="bi bi-person-check-fill fs-4 me-3"></i>
            <div>
                <strong>Atenção:</strong> Criando cadastro de bolsista vinculado ao usuário <strong>{{ $user->name }}</strong>.
                @if(is_null($user->email_verified_at))
                    <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-warning text-dark">E-mail não verificado</span>
                        <form method="POST" action="{{ route('admin.users.resend-verification', $user) }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-envelope"></i> Reenviar verificação de e-mail
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Formulário -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.scholarship_holders.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    <!-- SESSÃO: Vínculo de Usuário (Autocomplete) -->
                    <div class="col-12 bg-light p-4 rounded border">
                        <h5 class="mb-3 text-primary">
                            <i class="bi bi-person-bounding-box me-2"></i>Vínculo com Usuário do Sistema
                        </h5>

                        <div class="position-relative">
                            <label for="user_search" class="form-label fw-bold">Pesquisar Usuário Existente (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                <!-- Campo de texto para a pesquisa visual -->
                                <input type="text" id="user_search" class="form-control"
                                       placeholder="Digite o nome do usuário para vincular..."
                                       value="{{ isset($user) ? $user->name : old('user_search_name') }}"
                                       autocomplete="off">

                                <!-- Botão para limpar a seleção -->
                                <button class="btn btn-outline-secondary" type="button" id="clear_user_btn" title="Limpar Seleção">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            <!-- Input Oculto que realmente envia o ID para o banco -->
                            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id ?? old('user_id') }}">

                            <!-- Dropdown de resultados do Autocomplete -->
                            <ul id="user_search_results" class="list-group position-absolute w-100 shadow-sm" style="display:none; z-index: 1050; max-height: 200px; overflow-y: auto;">
                                <!-- Resultados da busca via JS aparecerão aqui -->
                            </ul>

                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle-fill text-info me-1"></i>
                                <strong>Fluxo Automático:</strong> Se você deixar este campo vazio, o sistema criará um usuário automaticamente utilizando o <strong>E-mail</strong> e <strong>CPF</strong> informados abaixo (a senha padrão será o CPF).
                            </div>
                        </div>
                    </div>

                    <!-- SESSÃO: Dados Pessoais -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-person me-2"></i>Dados Pessoais
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="cpf" class="form-label fw-bold">CPF <span class="text-danger">*</span></label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" class="form-control" required placeholder="000.000.000-00">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold">Telefone / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" placeholder="(00) 00000-0000">
                    </div>

                    <!-- SESSÃO: Atuação -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-briefcase me-2"></i>Dados de Atuação
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <label for="position" class="form-label fw-bold">Cargo / Posição <span class="text-danger">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" class="form-control" required placeholder="Ex: Apoio Administrativo">
                    </div>

                    <div class="col-md-4">
                        <label for="unit_id" class="form-label fw-bold">Unidade de Atuação <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-select" required>
                            <option value="">Selecione uma unidade...</option>
                            @foreach($units as $id => $name)
                                <option value="{{ $id }}" {{ old('unit_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-bold">Data de Início <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-bold">Data de Término</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control">
                    </div>

                    <!-- SESSÃO: Dados Bancários -->
                    <div class="col-12 mt-5">
                        <h5 class="border-bottom pb-2 mb-0 text-primary">
                            <i class="bi bi-bank me-2"></i>Dados Bancários (Opcional)
                        </h5>
                    </div>

                    <div class="col-md-3">
                        <label for="bank" class="form-label fw-bold">Banco</label>
                        <input type="text" name="bank" id="bank" value="{{ old('bank') }}" class="form-control" placeholder="Ex: Banco do Brasil">
                    </div>

                    <div class="col-md-3">
                        <label for="agency" class="form-label fw-bold">Agência</label>
                        <input type="text" name="agency" id="agency" value="{{ old('agency') }}" class="form-control" placeholder="Ex: 1234-5">
                    </div>

                    <div class="col-md-3">
                        <label for="account" class="form-label fw-bold">Conta</label>
                        <input type="text" name="account" id="account" value="{{ old('account') }}" class="form-control" placeholder="Corrente ou Poupança">
                    </div>

                    <div class="col-md-3">
                        <label for="pix_key" class="form-label fw-bold">Chave PIX</label>
                        <input type="text" name="pix_key" id="pix_key" value="{{ old('pix_key') }}" class="form-control" placeholder="Chave PIX">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user_search');
    const userIdInput = document.getElementById('user_id');
    const resultsList = document.getElementById('user_search_results');
    const clearBtn = document.getElementById('clear_user_btn');

    // Campos a serem preenchidos ao selecionar um usuário
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');

    let debounceTimer;

    // Função de limpeza
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        userIdInput.value = '';
        resultsList.style.display = 'none';
        nameInput.value = '';
        emailInput.value = '';
        searchInput.focus();
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value;

        if (query.length < 2) {
            resultsList.style.display = 'none';
            // Se apagar a pesquisa, apaga o ID oculto também para forçar a criação de um novo
            if(query.length === 0) userIdInput.value = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            // Nota: Você precisará criar uma rota de API real para essa busca funcionar.
            // Exemplo: Route::get('/api/users/search', [UserController::class, 'search']);
            fetch(`/api/users/search?q=${encodeURIComponent(query)}`)
                .then(response => response.ok ? response.json() : [])
                .then(data => {
                    resultsList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action cursor-pointer';
                            li.innerHTML = `<strong>${user.name}</strong> <br><small class="text-muted">${user.email}</small>`;
                            li.style.cursor = 'pointer';

                            li.addEventListener('click', () => {
                                searchInput.value = user.name;
                                userIdInput.value = user.id;
                                nameInput.value = user.name;
                                emailInput.value = user.email;
                                resultsList.style.display = 'none';
                            });

                            resultsList.appendChild(li);
                        });
                        resultsList.style.display = 'block';
                    } else {
                        resultsList.innerHTML = '<li class="list-group-item text-muted">Nenhum usuário encontrado.</li>';
                        resultsList.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar usuários:', error);
                });
        }, 300);
    });

    // Fecha a lista se clicar fora
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== resultsList) {
            resultsList.style.display = 'none';
        }
    });
});
</script>
@endpush
