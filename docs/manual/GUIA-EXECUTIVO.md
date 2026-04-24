# ProBolsas - Portal de Gestao de Bolsas

## Guia Executivo do Sistema (v1)

Documento orientado para gestao, implantacao e operacao.  
Baseado nas funcionalidades atualmente disponiveis no sistema.

## 1. Objetivo do Documento

1. Consolidar visao funcional do ProBolsas
2. Definir responsabilidades por perfil
3. Padronizar rotina operacional
4. Reduzir risco em homologacao, pagamento e cadastros criticos

## 2. Escopo Funcional Atual

1. Frequencia e submissao mensal do bolsista
2. Homologacao por coordenacao
3. Gestao academica (projetos, cursos, disciplinas, turmas)
4. Gestao de usuarios, papeis e permissoes
5. Fluxo financeiro e pagamentos
6. Relatorios operacionais e financeiros

## 3. Perfis e Responsabilidades

## 3.1 Bolsista

1. Registrar frequencia
2. Submeter frequencia mensal
3. Emitir relatorios
4. Acompanhar pagamentos

## 3.2 Coordenacao (adjunto, adjunto geral, geral)

1. Homologar/rejeitar submisses
2. Aplicar filtros por escopo (unidade/instituicao)
3. Monitorar pendencias operacionais
4. Apoiar consistencia de dados para fechamento

## 3.3 Administrador

1. Manter estrutura institucional e academica
2. Gerenciar usuarios, papeis e permissoes
3. Governar processos financeiros e auditoria
4. Controlar configuracoes e padroes do sistema

## 3.4 Professor/Supervisor

1. Apoiar acompanhamento academico conforme vinculo
2. Validar contexto de unidade/curso/turma
3. Apoiar coordenacao na consistencia de informacoes

## 4. Fluxos Criticos

## 4.1 Fluxo 01 - Frequencia Mensal

1. Bolsista registra atividades
2. Bolsista envia submissao do periodo
3. Coordenacao analisa e decide (aprovar/rejeitar)
4. Resultado segue para trilha de consolidacao

## 4.2 Fluxo 02 - Homologacao

1. Coordenacao filtra por mes/status/unidade/projeto/bolsista
2. Avalia registros e evidencias
3. Homologa individual ou em lote
4. Registra justificativa em rejeicao

## 4.3 Fluxo 03 - Pagamento

1. Operacao financeira consolida dados homologados
2. Processa lotes e status de pagamento
3. Bolsista acompanha e confirma recebimento
4. Gestao audita relatorios por periodo

## 4.4 Fluxo 04 - Governanca Academica

1. Administracao organiza projetos e vinculos
2. Configura cursos, disciplinas e turmas
3. Mantem docentes, supervisores e bolsistas vinculados
4. Revisa consistencia por unidade e instituicao

## 5. Matriz de Acesso (Resumo)

| Modulo | Bolsista | Coordenacao | Admin |
|---|---|---|---|
| Frequencia (registro) | Sim | Consulta/controle | Sim |
| Submissoes mensais | Sim | Sim (homologa) | Sim |
| Homologacoes | Nao | Sim | Sim |
| Pagamentos | Consulta propria | Painel operacional | Operacao completa |
| Cursos/Disciplinas/Turmas | Nao | Parcial por escopo | Sim |
| Usuarios/Papeis | Nao | Restrito | Sim |

## 6. Indicadores Minimos de Operacao

1. Percentual de submissoes enviadas no prazo
2. Tempo medio de homologacao por ciclo
3. Percentual de rejeicoes por motivo
4. Tempo medio de processamento de pagamento
5. Taxa de retrabalho em cadastros academicos

## 7. Checklist Operacional Mensal

1. Validar periodo de referencia (mes/ano)
2. Fechar pendencias de submissao
3. Executar homologacao e revisar rejeicoes
4. Consolidar financeiro e comprovantes
5. Exportar relatorios de auditoria
6. Registrar ocorrencias e acoes corretivas

## 8. Riscos e Controles

1. Risco: homologacao em periodo incorreto
   Controle: filtros obrigatorios + conferenca dupla
2. Risco: permissao excessiva
   Controle: revisao mensal de papeis
3. Risco: divergencia de vinculos academicos
   Controle: checklist de consistencia por turma
4. Risco: retrabalho no financeiro
   Controle: validar status antes de lote

## 9. Plano de Evolucao do Manual

1. Adicionar capturas de tela por fluxo
2. Adicionar fluxogramas operacionais
3. Publicar procedimento de incidentes
4. Criar versao com trilha de treinamento

## 10. Referencias Internas

1. [Manual Geral](./README.md)
2. [Guia do Bolsista](./perfis/bolsista.md)
3. [Guia da Coordenacao](./perfis/coordenacao.md)
4. [Guia do Administrador](./perfis/admin.md)
5. [Guia Professor/Supervisor](./perfis/professor-supervisor.md)
6. [Guia Turmas/Alunos](./perfis/turmas-alunos.md)

## Regras Criticas do Sistema

- Fluxo mensal sequencial (nao pode pular meses)
- Rejeicao retorna pacote completo
- Pagamentos dependem de submissao valida
- Filtros definem o contexto das operacoes

## 📊 Funcionalidades do Sistema

- Gestao de bolsistas e frequencia
- Gestao academica de turmas e alunos
- Submissao mensal sequencial
- Controle financeiro (bolsistas e alunos)
- Dashboards e relatorios exportaveis
- Filtros inteligentes (unidade → curso → turma)