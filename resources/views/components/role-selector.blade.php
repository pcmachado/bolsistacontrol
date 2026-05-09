{{-- Componente para seleção de roles com hierarquia --}}
@props([
    'selectedRoles' => [],
    'user' => null,
    'multiple' => true,
    'required' => false,
    'name' => 'role'
])

@php
    $hierarchy = [
        'superadmin' => ['name' => 'Super Administrador', 'weight' => 100, 'color' => 'danger', 'description' => 'Acesso total ao sistema'],
        'admin' => ['name' => 'Administrador', 'weight' => 90, 'color' => 'warning', 'description' => 'Gerenciamento administrativo'],
        'coordenador_geral' => ['name' => 'Coordenador Geral', 'weight' => 70, 'color' => 'info', 'description' => 'Coordenação geral institucional'],
        'coordenador_adjunto_geral' => ['name' => 'Coordenador Adjunto Geral', 'weight' => 60, 'color' => 'primary', 'description' => 'Suporte à coordenação geral'],
        'coordenador_adjunto' => ['name' => 'Coordenador Adjunto', 'weight' => 30, 'color' => 'secondary', 'description' => 'Coordenação adjunta'],
        'bolsista' => ['name' => 'Bolsista', 'weight' => 10, 'color' => 'success', 'description' => 'Usuário bolsista'],
    ];

    $currentUser = $user ?? auth()->user();
    $maxWeight = 0;

    if ($currentUser) {
        foreach ($currentUser->roles as $role) {
            $maxWeight = max($maxWeight, $hierarchy[$role->name]['weight'] ?? 0);
        }
    }

    // Filtrar roles que o usuário pode atribuir (peso menor ou igual)
    $availableRoles = collect($hierarchy)->filter(function ($config) use ($maxWeight, $currentUser) {
        // Superadmin pode tudo
        if ($currentUser && $currentUser->hasRole('superadmin')) {
            return true;
        }
        // Outros usuários só podem atribuir roles de peso menor ou igual
        return $config['weight'] <= $maxWeight;
    });
@endphp

<div class="form-group">
    <label for="roles-selector" class="form-label">
        <strong>{{ $multiple ? 'Funções' : 'Função' }}</strong>
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($multiple)
        <select
            name="{{ $name }}[]"
            id="roles-selector"
            class="form-control"
            {{ $multiple ? 'multiple' : '' }}
            {{ $required ? 'required' : '' }}
            style="min-height: 120px;"
        >
            @foreach($availableRoles as $roleKey => $config)
                <option
                    value="{{ $roleKey }}"
                    {{ in_array($roleKey, $selectedRoles) ? 'selected' : '' }}
                    data-weight="{{ $config['weight'] }}"
                    data-description="{{ $config['description'] }}"
                    class="role-option-{{ $config['color'] }}"
                >
                    {{ $config['name'] }} ({{ $config['weight'] }})
                </option>
            @endforeach
        </select>
    @else
        <select
            name="{{ $name }}"
            id="roles-selector"
            class="form-control"
            {{ $required ? 'required' : '' }}
        >
            <option value="">Selecione uma função</option>
            @foreach($availableRoles as $roleKey => $config)
                <option
                    value="{{ $roleKey }}"
                    {{ in_array($roleKey, $selectedRoles) ? 'selected' : '' }}
                    data-weight="{{ $config['weight'] }}"
                    data-description="{{ $config['description'] }}"
                >
                    {{ $config['name'] }}
                </option>
            @endforeach
        </select>
    @endif

    <small class="form-text text-muted">
        @if($multiple)
            Selecione uma ou mais funções. Cada função tem um nível de permissão diferente.
        @else
            Selecione a função principal do usuário.
        @endif
    </small>

    @if($multiple)
        <div id="selected-roles-info" class="mt-2" style="display: none;">
            <small class="text-info">
                <i class="fas fa-info-circle"></i>
                <span id="role-description"></span>
            </small>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('roles-selector');

    @if($multiple)
        // Para select múltiplo, mostrar descrição da role selecionada
        roleSelector.addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions);
            const infoDiv = document.getElementById('selected-roles-info');
            const descriptionSpan = document.getElementById('role-description');

            if (selectedOptions.length > 0) {
                const lastSelected = selectedOptions[selectedOptions.length - 1];
                const description = lastSelected.getAttribute('data-description');
                descriptionSpan.textContent = description;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        });
    @else
        // Para select simples, mostrar descrição ao selecionar
        roleSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description');

            if (description && this.value) {
                // Criar ou atualizar tooltip
                this.title = description;
            } else {
                this.title = '';
            }
        });
    @endif
});
</script>
@endpush

<style>
.role-option-danger { color: #dc3545; }
.role-option-warning { color: #ffc107; }
.role-option-info { color: #0dcaf0; }
.role-option-primary { color: #0d6efd; }
.role-option-secondary { color: #6c757d; }
.role-option-success { color: #198754; }
</style>