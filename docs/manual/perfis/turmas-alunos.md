# Manual de Turmas e Alunos

## Objetivo

Gerenciar lancamentos academicos mensais dos alunos e envio ao financeiro.

---

## Estrutura do Fluxo

Turma → Alunos → Lancamentos → Submissao → Pagamentos

---

## Lancamentos (Mensal)

Para cada aluno:

1. Total de aulas
2. Faltas
3. Aulas assistidas (automatico)
4. Valor diario (projeto)
5. Valor total (automatico)

---

## Submissao da Turma

Fluxo:

draft → submitted → approved/rejected

---

## Regras Importantes

1. Mes atual depende do anterior
2. Nao e possivel enviar um mes sem envio do anterior
3. Rejeicao retorna o pacote completo para correcao
4. Pagamentos sao removidos em caso de rejeicao

---

## Navegacao por Mes

1. Apenas meses dentro do periodo da turma
2. Visualizacao com status:

- draft (aberto)
- submitted (enviado)
- approved (aprovado)
- rejected (rejeitado)
- bloqueado (mes anterior nao enviado)

---

## Rotina Recomendada

1. Lancar dados semanalmente
2. Validar antes de enviar
3. Garantir sequencia mensal correta
4. Acompanhar status da submissao

---

## Erros Comuns

1. Tentar enviar mes fora de sequencia
2. Dados inconsistentes (faltas > aulas)
3. Nao revisar antes de enviar

---

## Boas Praticas

1. Validar dados por turma antes do envio
2. Utilizar filtros por unidade/curso/turma
3. Acompanhar rejeicoes com prioridade