# 📊 ANÁLISE COMPLETA DO SISTEMA - MAIO 2026

## 🎯 SUMÁRIO EXECUTIVO

Este documento apresenta uma análise detalhada do sistema **ProBolsas** com foco em:
- Identificação de erros e inconsistências
- Melhorias estruturais propostas
- Duplicações e código desnecessário
- Análise de rotas, views e controllers
- Consistência de navegação

---

## 1️⃣ PROBLEMAS IDENTIFICADOS NA BLADE `risk.blade.php`

### ❌ Problemas Críticos

#### 1.1 - HTML Inválido (Usar `<tr>` fora de `<table>`)
```blade
<div class="row g-3">
    @foreach($data as $d)
    <tr onclick="...">  {{-- ❌ ERRO: <tr> não pode estar dentro de <div> --}}
        <td>Aluno {{ $d['student_id'] }}</td>
```

**Impacto**: Renderização quebrada, sem nenhuma estrutura visual adequada

#### 1.2 - Falta de Informações Contextuais
- Não mostra qual turma/disciplina os dados se referem
- Nenhuma indicação de data/período
- Falta correlação com o modelo completo

#### 1.3 - Falta de Interatividade
- Sem busca/filtro de alunos
- Sem exportação de dados
- Sem ações rápidas (e-mail, notificação)

---

## 2️⃣ MELHORIAS PROPOSTAS PARA MONITORAMENTO DE RISCO

### 📌 PRIORIDADE 1: Corrigir e Expandir Risk View

#### 2.1 - Alertas Automáticos para Coordenadores ✅
**Quando**: Aluno atinge 25% de faltas (crítico) ou 15% (risco)
**Quem**: Coordenador da turma/projeto
**Como**: 
- Notificação em DB + Email
- Job agendado: `NotifyStudentRiskAlert` (diariamente)
- Setting em `notification_settings` para configurar threshold

**Tabelas necessárias**: Já existem (`notification_settings`, `notifications`)

---

#### 2.2 - Ranking de Turmas Críticas 📊
**O que mostrar**:
1. Turmas com maior % de alunos em risco
2. Turmas com abandono detectado
3. Tendência semanal (piora/melhora)

**Nova rota**: `admin.dashboard.academic.critical-classes`
**Dados necessários**: 
- Contar alunos por nivel (critical/risk/warning)
- Calcular trend de 4 semanas

---

#### 2.3 - Previsão de Evasão 🔮
**Algoritmo**:
```
IF absences > 40% THEN evasão_alta
IF absences aumentou 10% em 2 semanas THEN risco_evasão
IF ausências >= 75% de 1 mês THEN abandono_confirmado
```

**Dados a rastrear**:
- Histórico de faltas/mês
- Última data de presença
- Velocidade de degradação

---

## 3️⃣ ANÁLISE DE ROTAS, VIEWS E CONTROLLERS

### 🔴 DUPLICAÇÕES ENCONTRADAS

#### 3.1 - Rotas de Dashboard Acadêmico
```
❌ POST   admin/dashboard.academic.risk       → rota sem método GET
✅ GET    admin/dashboard/academic            → método index
```
**Problema**: Rota POST incompleta. Não há um `show` para risco específico.

#### 3.2 - Pagamentos (3 dashboards diferentes!)
```
✅ admin/payments/dashboard           (PaymentDashboardController)
✅ admin/student-payments/dashboard   (StudentPaymentController)
❌ payments.dashboard (rota duplicada em routes)
```
**Problema**: Confusão entre payment (bolsista) e student payment. UI inconsistente.

#### 3.3 - Frequência (2 fluxos sobrepostos)
```
❌ attendance/submissions/my    (MyAttendanceSubmissionController)
✅ attendance/submissions/      (AttendanceSubmissionController)
❌ Mesma lógica em 2 controllers diferentes
```
**Problema**: Código duplicado, difícil de manter.

---

### 🟡 CONTROLLERS COM RESPONSABILIDADE EXCESSIVA

| Controller | Linhas | Métodos | Problema |
|-----------|--------|---------|----------|
| `AcademicDashboardController` | ~150 | 2 | OK |
| `ClassOfferingController` | ~300+ | 7+ | CRUD + Dashboard mesclado |
| `AdminScholarshipHolderController` | ~200 | 5+ | Impersonate + CRUD + Edição |
| `StudentPaymentController` | ~250 | 6+ | Dashboard + Relatório + Batch |

**Recomendação**: Separar em controllers específicos
- `ClassOfferingDashboardController` ✅ (já existe)
- `ScholarshipHolderDashboardController` (novo)
- `PaymentReportController` (novo)

---

## 4️⃣ ANÁLISE DE VIEWS E ESTRUTURA

### 📂 Estrutura de Views

```
✅ resources/views/
   ├── layouts/
   │   ├── app.blade.php
   │   └── partials/
   │       └── _sidebar.blade.php
   ├── admin/dashboard/academic/
   │   ├── index.blade.php      (dashboard principal)
   │   └── risk.blade.php       (❌ QUEBRADO)
   ├── teacher/classes/
   │   ├── index.blade.php      ✅ Com filtros
   │   └── show.blade.php       ✅ Recém atualizado
   └── ... [36 pastas diferentes]
```

**Problema**: Views não organizado, difícil encontrar.
**Sugestão**: Agrupar por funcionalidade
```
resources/views/
├── academic/              (novo)
│   ├── risk/
│   ├── classes/
│   └── dashboard/
├── financial/
│   ├── payments/
│   ├── reports/
│   └── dashboard/
└── shared/
```

---

## 5️⃣ ANÁLISE DE SIDEBAR

### 🔴 PROBLEMAS IDENTIFICADOS

#### 5.1 - Itens Duplicados
```blade
<!-- Aparece 2x -->
<li><x-sidebar-item route="admin.payments.index" /></li>
<!-- Aparece em 2 seções diferentes -->
```

#### 5.2 - Inconsistência de Ícones
```blade
📊 Dashboard          (icon: bi bi-speedometer2)
📊 Dashboard Admin    (icon: bi bi-speedometer)  ← Ícone diferente
📊 Financeiro         (icon: bi bi-graph-up)
```

#### 5.3 - Seções Redundantes
```
"Minha Área" (bolsista)
"Coordenação"
"Gestão"
"Professor"

← Confusão: professor é role, coordenação/gestão é função admin
```

---

## 6️⃣ INCONSISTÊNCIAS NA AUTORIZAÇÃO

### 🔐 Problemas de Acesso

#### 6.1 - Métodos Não Padronizados
```php
// Em User.php
if(auth()->user()->canAccessTeacher())       ✅ Custom
if(auth()->user()->canAccessCoordination())  ✅ Custom
if(auth()->user()->hasAnyRole([...]))        ✅ Spatie
if(auth()->user()->role(...))                ✅ Diferente sintaxe
```

**Problema**: Mistura de 3 abordagens diferentes

#### 6.2 - Rota de Risco Sem Proteção?
```php
Route::post('academic.risk', [AcademicDashboardController::class, 'risk'])
// ❌ Sem middleware específico, usa genérico: 'role_or_permission:...'
```

---

## 7️⃣ CHECKLIST DE MELHORIAS

### Prioridade Alta 🔴

- [ ] **Corrigir risk.blade.php** - Substituir `<tr>` por cards/table válido
- [ ] **Implementar alertas de risco** - NotifyStudentRiskAlert job
- [ ] **Reunir pagamentos** - Unificar Student/Scholarship payment
- [ ] **Deduplicar attendance** - Remover MyAttendanceSubmissionController

### Prioridade Média 🟡

- [ ] **Reorganizar sidebar** - Remover duplicatas, padronizar ícones
- [ ] **Criar academic dashboard específico** - Separar risk, classes, etc
- [ ] **Consolidar autorizações** - Usar apenas método padrão

### Prioridade Baixa 🟢

- [ ] **Renomear rotas** - Mais consistência (academic_risk vs dashboard.academic.risk)
- [ ] **Documentar fluxos** - Especialmente frequência/pagamento/homologação
- [ ] **Adicionar testes** - Controllers novos precisam de coverage

---

## 8️⃣ IMPLEMENTAÇÕES RECOMENDADAS

### 🚀 Quick Wins (Semana 1)

1. **Fix risk.blade.php** - 30min
2. **Remove sidebar duplicatas** - 30min
3. **Create StudentRiskNotification** - 2h
4. **Add risk ranking view** - 3h

### 📊 Medium-term (Semana 2-3)

1. **Create Academic Risk Service** (AcademicRiskService melhorada)
2. **Create ChurnPredictionService** - Prever evasão
3. **Refactor payment controllers**
4. **Add risk trend charting** - Gráfico de evolução

---

## 📋 RESUMO DE AÇÕES

| Item | Status | Prioridade | Esforço |
|------|--------|-----------|---------|
| Corrigir risk.blade.php | ⏳ | 🔴 | 30min |
| Alertas automáticos risco | ⏳ | 🔴 | 2h |
| Ranking de turmas críticas | ⏳ | 🟡 | 3h |
| Previsão de evasão | ⏳ | 🟡 | 4h |
| Sidebar deduplicado | ⏳ | 🟡 | 1h |
| Controllers refactor | ⏳ | 🟢 | 6h |

---

**Data da Análise**: 7 de Maio de 2026
**Versão**: 1.0
