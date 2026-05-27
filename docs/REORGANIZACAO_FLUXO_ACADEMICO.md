# Reorganização do fluxo acadêmico e financeiro (turma, disciplina, professor e aluno)

## Objetivo

Formalizar uma estrutura única para:

- tratar **teacher** como um tipo de bolsista (não como cadastro paralelo);
- organizar a execução acadêmica por **turma** (oferta de curso dentro de projeto/unidade);
- registrar presença e frequência **mensal** por aluno/disciplina;
- consolidar o fechamento mensal para cálculo de pagamentos e envio ao financeiro.

---

## Regras de domínio

1. **Professor é bolsista**
   - Todo professor deve existir em `scholarship_holders`.
   - O papel de professor é dado por:
     - `position` (cargo professor), e
     - `is_teacher = true`.

2. **Turma representa a execução de um curso em um contexto**
   - A turma pertence a:
     - projeto,
     - unidade,
     - curso.

3. **Disciplinas da turma**
   - Cada turma possui várias disciplinas.
   - Cada disciplina da turma tem:
     - carga horária total planejada,
     - professor responsável (bolsista com flag/cargo de professor).

4. **Alunos por turma**
   - O aluno pertence à turma.
   - O acompanhamento diário detalhado não é obrigatório neste fluxo.

5. **Registro mensal por aluno/disciplina**
   - Para cada mês e disciplina:
     - total de aulas no mês,
     - faltas,
     - faltas justificadas,
     - presenças.

6. **Controle de horas da turma**
   - A turma pode ter `hours_per_day` para apoiar:
     - evolução da disciplina,
     - comparação entre horas executadas no mês e carga total.

7. **Consolidado mensal**
   - Uma grade mensal por turma deve consolidar, por aluno:
     - disciplinas cursadas no mês,
     - aulas do mês por disciplina,
     - faltas e faltas justificadas,
     - totais gerais.
   - O consolidado deve calcular o valor previsto de pagamento do aluno no mês e disponibilizar para o financeiro.

---

## Modelo lógico sugerido

### Entidades principais

- `class_offerings` (turmas)
  - `project_id`, `unit_id`, `course_id`
  - `hours_per_day` (decimal, opcional)

- `class_offering_disciplines` (disciplinas ofertadas na turma)
  - `class_offering_id`, `discipline_id`
  - `teacher_scholarship_holder_id`
  - `planned_total_hours`

- `class_offering_students` (alunos da turma)
  - `class_offering_id`, `scholarship_holder_id`

- `student_discipline_month_records` (fechamento mensal do aluno por disciplina)
  - `class_offering_id`
  - `class_offering_discipline_id`
  - `scholarship_holder_id` (aluno)
  - `reference_month` (YYYY-MM)
  - `classes_in_month`
  - `absences`
  - `justified_absences`
  - `presences`

- `student_month_records` (consolidado mensal por aluno)
  - `class_offering_id`
  - `scholarship_holder_id`
  - `reference_month`
  - `total_classes`
  - `total_absences`
  - `total_justified_absences`
  - `total_presences`
  - `estimated_payment_amount`
  - `status` (draft/closed/sent_to_finance)

---

## Regras de integridade importantes

1. `teacher_scholarship_holder_id` deve apontar para bolsista com:
   - `is_teacher = true`, e
   - cargo compatível com professor.

2. Em `student_discipline_month_records`:
   - `presences = classes_in_month - absences` (ou fórmula equivalente acordada),
   - `justified_absences <= absences`.

3. Unicidade sugerida:
   - `UNIQUE(class_offering_discipline_id, scholarship_holder_id, reference_month)`
     para evitar dois fechamentos mensais da mesma disciplina/aluno.

4. Consolidado mensal:
   - `UNIQUE(class_offering_id, scholarship_holder_id, reference_month)`.

---

## Fluxo operacional mensal

1. Coordenação define/atualiza disciplinas da turma e professores.
2. Durante o mês, registra-se somente o total mensal por disciplina/aluno.
3. No fechamento:
   - calcular totais por aluno,
   - gerar `student_month_records`,
   - calcular valor previsto do mês,
   - marcar status para envio ao financeiro.
4. Financeiro consome consolidados `sent_to_finance`.

---

## Benefícios da reorganização

- Elimina duplicidade de cadastro de professor fora da base de bolsistas.
- Padroniza o fechamento mensal acadêmico e financeiro.
- Melhora rastreabilidade entre turma -> disciplina -> professor -> aluno -> pagamento.
- Facilita dashboards de frequência, progresso de carga horária e previsão financeira.
