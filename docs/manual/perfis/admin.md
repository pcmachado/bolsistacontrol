# Manual do Administrador

## Objetivo do Perfil

Administrar estrutura institucional, usuarios, operacao academica e fluxo financeiro.

## Modulos Principais

1. Usuarios, papeis e permissoes
2. Instituicoes e unidades
3. Projetos, cursos, disciplinas e turmas
4. Bolsistas, professores e supervisores
5. Homologacoes e pagamentos
6. Relatorios financeiros e institucionais

## Modulo de Turmas e Alunos

1. Configuracao de valor diario por projeto
2. Criacao de turmas com periodo definido
3. Vinculacao de alunos as turmas
4. Controle de consistencia dos lancamentos

## Financeiro de Alunos

1. Geracao de pagamentos a partir de lancamentos
2. Controle de status (pending, sent, overdue, paid)
3. Exportacao de relatorios
4. Auditoria de valores por periodo

## Acessos-Chave

1. `admin.users.index`
2. `admin.roles.index`
3. `admin.permissions.index`
4. `admin.institutions.index`
5. `admin.units.index`
6. `admin.projects.index`
7. `admin.courses.index`
8. `admin.disciplines.index`
9. `admin.class-offerings.index`
10. `admin.payments.index`

## Rotina Recomendada

1. Validar cadastros mestres (instituicoes/unidades/cargos)
2. Revisar perfis e permissoes periodicamente
3. Auditar turmas e vinculos ativos
4. Consolidar dados financeiros por periodo

## Operacoes Criticas (Checklist)

1. Criacao/edicao de papeis: revisar impacto em visibilidade
2. Alteracao de unidades/instituicoes: conferir filtros e escopos
3. Atualizacao de turmas/projetos: validar relacionamento curso-projeto
4. Processamento de pagamento: confirmar status e comprovantes

## Riscos Operacionais

1. Permissao excessiva para usuarios operacionais
2. Filtro inadequado antes de acao em lote
3. Inconsistencia de vinculacoes academicas

## Boas Praticas

1. Alteracoes estruturais somente com janela de manutencao
2. Uso de relatorios para conciliacao mensal
3. Registro de decisoes em operacoes sensiveis
