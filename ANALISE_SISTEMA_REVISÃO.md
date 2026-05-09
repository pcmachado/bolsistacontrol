# 📋 Análise Geral do Sistema - Revisão de Filtragens e Gestão de Usuários

## 🎯 **Objetivos da Revisão**

1. **Filtragens por Instituição e Projeto**: Verificar consistência e eficiência
2. **Gestão de Usuários**: Profile, User, Position, Role e Permission
3. **Consistência**: Padronização de interfaces e lógica
4. **Facilidade de Gerenciamento**: Melhorar UX para designação de permissões

---

## 🔍 **Análise Atual - Pontos Fortes**

### ✅ **Sistema de Roles e Permissions**
- Usa Spatie Laravel Permission (excelente escolha)
- Hierarquia bem definida no RoleController
- Controle granular de permissões

### ✅ **Modelo User**
- Relações bem estruturadas (unit, institutions, scholarshipHolder)
- Métodos helpers para roles (isAdmin, isCoordenador, etc.)
- Lógica de contexto institucional implementada

### ✅ **VisibilityService**
- Sistema robusto de filtragem por instituição/projeto
- Diferentes níveis de escopo (superadmin, institution, unit, assignment)
- Aplicação consistente em queries

### ✅ **DataTables**
- Filtragem implementada no UsersDataTable
- Controle de visibilidade por instituição

---

## ⚠️ **Problemas Identificados**

### 1. **Filtragens Inconsistentes**

**Problema**: Nem todos os DataTables aplicam filtragem por instituição
```php
// UsersDataTable - OK
if ($logged->isInstitutionScoped()) {
    $query->whereIn('institution_id', $institutionIds);
}

// Outros DataTables podem não ter essa lógica
```

**Problema**: Filtros de projeto não são aplicados consistentemente
```php
// Alguns controllers fazem filtragem manual
$projectId = $request->input('project_id');
// Outros não implementam
```

### 2. **Gestão de Roles/Permissions**

**Problema**: Interface de edição de roles não mostra permissões atuais
```php
// RoleController@edit - não carrega permissões
public function edit(Role $role)
{
    return view('admin.roles.edit', compact('role'));
    // Deveria: compact('role', 'permissions', 'rolePermissions')
}
```

**Problema**: Não há validação de hierarquia ao atribuir roles
```php
// User pode atribuir roles superiores se não houver validação
```

### 3. **Modelo Position**

**Problema**: Position não está integrado ao sistema de permissões
```php
// Position deveria ter relação com roles
// Atualmente apenas tem name, description, is_teacher
```

### 4. **Profile Management**

**Problema**: ProfileController não filtra instituições acessíveis
```php
// Não considera contexto institucional do usuário
```

### 5. **Inconsistência Visual**

**Problema**: Formulários de usuário não seguem padrão consistente
- Alguns campos obrigatórios não marcados
- Validações diferentes entre create/edit
- UX de seleção de roles pode ser melhorada

---

## 🚀 **Propostas de Melhoria**

### 1. **Padronizar Filtragens por Instituição**

Criar um **DataTable Base** com filtragem automática:

```php
abstract class BaseDataTable extends DataTable
{
    protected function applyInstitutionFilter($query, $user)
    {
        if ($user->isInstitutionScoped()) {
            $institutionIds = $user->activeInstitutionIds();
            // Aplicar filtro baseado no modelo
        }
    }
}
```

### 2. **Melhorar Gestão de Roles**

**Interface de Edição de Roles:**
```php
public function edit(Role $role)
{
    $permissions = Permission::all();
    $rolePermissions = $role->permissions->pluck('id')->toArray();
    
    return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
}
```

**Validação de Hierarquia:**
```php
public function update(Request $request, User $user)
{
    // Validar se usuário pode atribuir a role
    $this->authorizeRoleAssignment($request->role, Auth::user());
}
```

### 3. **Integrar Position com Roles**

```php
// No modelo Position
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}

// Método para auto-atribuir roles baseado na position
public function assignDefaultRoles(User $user)
{
    foreach ($this->roles as $role) {
        $user->assignRole($role);
    }
}
```

### 4. **Melhorar Profile Management**

**Adicionar contexto institucional:**
```php
public function edit(Request $request): View
{
    $user = $request->user();
    $accessibleInstitutions = $user->accessibleInstitutionIds();
    
    return view('profile.edit', compact('user', 'accessibleInstitutions'));
}
```

### 5. **Interface Unificada de Usuários**

**Componente de Seleção de Roles:**
```php
// resources/views/components/role-selector.blade.php
@props(['selectedRoles' => [], 'hierarchy' => []])

<select name="roles[]" multiple class="form-control" id="roles">
    @foreach($hierarchy as $role => $weight)
        <option value="{{ $role }}" 
                {{ in_array($role, $selectedRoles) ? 'selected' : '' }}
                data-weight="{{ $weight }}">
            {{ ucfirst(str_replace('_', ' ', $role)) }}
        </option>
    @endforeach
</select>
```

---

## 📋 **Plano de Implementação**

### **Fase 1: Filtragens (Prioridade Alta)**
1. Criar BaseDataTable com filtragem automática
2. Atualizar todos os DataTables existentes
3. Implementar filtro de projeto consistente

### **Fase 2: Gestão de Roles (Prioridade Alta)**
1. Melhorar interface de edição de roles
2. Adicionar validação de hierarquia
3. Criar componente de seleção de roles

### **Fase 3: Position Integration (Prioridade Média)**
1. Integrar Position com Roles
2. Auto-atribuição de roles por position
3. Interface de gestão de positions

### **Fase 4: Profile Enhancement (Prioridade Média)**
1. Adicionar contexto institucional
2. Melhorar validações
3. Interface mais intuitiva

### **Fase 5: UX/UI Improvements (Prioridade Baixa)**
1. Padronizar formulários
2. Melhorar feedback visual
3. Adicionar tooltips explicativos

---

## 🔧 **Implementações Imediatas**

Vou começar implementando as melhorias críticas identificadas.