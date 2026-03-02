# Manual da Coordenacao

Perfis atendidos: `coordenador_adjunto`, `coordenador_adjunto_geral`, `coordenador_geral`.

## Objetivo do Perfil

Validar frequencias, acompanhar indicadores academicos e financeiros, e manter conformidade do processo.

## Acessos Principais

1. `admin.homologations.index` - Homologacoes
2. `admin.dashboard` - Dashboard administrativo
3. `admin.payments.dashboard` - Painel financeiro
4. `admin.class-offerings.index` - Turmas
5. `admin.projects.index` - Projetos

## Rotina Recomendada

1. Conferir submissao pendente por periodo
2. Homologar/rejeitar com justificativa quando necessario
3. Revisar pendencias por unidade/projeto
4. Acompanhar indicadores de pagamento

## Homologacao de Frequencia (Passo a Passo)

1. Acesse `admin.homologations.index`
2. Aplique filtros: mes, status, unidade, projeto, bolsista
3. Abra submissao para analise
4. Execute aprovacao ou rejeicao
5. Em lote, use acao `bulk` quando aplicavel

## Filtros e Escopo

1. Coordenacao geral: visao institucional
2. Coordenacao adjunta geral: visao institucional (conforme regra)
3. Coordenacao adjunta: escopo de unidades vinculadas

## Erros Comuns

1. Homologar mes errado por filtro incorreto
2. Rejeitar sem justificativa clara
3. Nao revisar status de pendentes apos operacao em lote

## Boas Praticas

1. Homologar por ciclos fixos (ex.: semanal no fechamento)
2. Padronizar justificativas de rejeicao
3. Exportar evidencia mensal para auditoria

