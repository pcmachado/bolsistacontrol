# Manual do Sistema de Notificações

## Visão Geral

O sistema de notificações do Bolsista Control permite configurar notificações automáticas para eventos importantes do sistema, como mudanças de status de pagamentos e submissões de frequência. As notificações podem ser enviadas por email e/ou armazenadas no banco de dados para visualização na interface.

## Funcionalidades Principais

### 1. Templates de Email Editáveis
Permite criar e editar templates de email personalizados para diferentes tipos de notificações.

### 2. Configurações de Notificações Granulares
Permite configurar quais notificações serão enviadas, para quais usuários e em quais condições.

## Acesso ao Sistema

Para acessar as funcionalidades de notificações, você deve ter uma das seguintes roles:
- **Super Admin**: Acesso completo a todas as funcionalidades
- **Admin**: Acesso administrativo completo
- **Coordenador Geral**: Acesso administrativo completo

## Como Configurar Notificações

### Passo 1: Acesse as Configurações

1. Faça login no sistema
2. No menu lateral, clique em **"Administração"**
3. Selecione **"Config. Notificações"**

### Passo 2: Criar uma Nova Configuração

1. Clique no botão **"Criar Configuração"**
2. Preencha os campos obrigatórios:
   - **Tipo de Evento**: Selecione o evento que disparará a notificação
   - **Tipo de Notificação**: Escolha entre:
     - *Banco de Dados*: Apenas notificação interna
     - *Email*: Apenas envio por email
     - *Ambos*: Notificação interna + email

### Passo 3: Definir Destinatários

Selecione quais roles (perfis) devem receber a notificação:
- Bolsista
- Orientador
- Coordenador
- Admin
- Etc.

### Passo 4: Definir Escopo (Opcional)

- **Projeto**: Limita a notificação a um projeto específico
- **Instituição**: Limita a notificação a uma instituição específica
- Deixe em branco para configuração global

### Passo 5: Ativar/Desativar

Marque a opção **"Habilitado"** para ativar a configuração.

## Eventos Disponíveis

| Evento | Descrição |
|--------|-----------|
| Mudança de Status de Pagamento | Quando um pagamento muda de status |
| Submissão de Frequência Enviada | Quando uma submissão é enviada |
| Submissão de Frequência Aprovada | Quando uma submissão é aprovada |
| Submissão de Frequência Rejeitada | Quando uma submissão é rejeitada |

## Templates de Email

### Como Criar um Template

1. No menu lateral, clique em **"Administração"**
2. Selecione **"Templates de Email"**
3. Clique em **"Criar Template"**
4. Preencha os campos:
   - **Chave**: Identificador único do template
   - **Nome**: Nome descritivo
   - **Assunto**: Assunto do email
   - **Corpo HTML**: Conteúdo do email em HTML
   - **Corpo Texto**: Versão texto plano do email

### Variáveis Disponíveis

Use as seguintes variáveis nos templates:

- `{{user_name}}` - Nome do usuário
- `{{user_email}}` - Email do usuário
- `{{project_name}}` - Nome do projeto
- `{{institution_name}}` - Nome da instituição
- `{{status}}` - Status atual
- `{{old_status}}` - Status anterior (para mudanças)
- `{{submission_date}}` - Data da submissão
- `{{payment_amount}}` - Valor do pagamento

### Exemplo de Template

**Assunto:** Status do seu pagamento foi atualizado

**Corpo HTML:**
```html
<p>Olá {{user_name}},</p>

<p>O status do seu pagamento foi alterado de <strong>{{old_status}}</strong> para <strong>{{status}}</strong>.</p>

<p>Projeto: {{project_name}}</p>
<p>Instituição: {{institution_name}}</p>

<p>Atenciosamente,<br>
Equipe Bolsista Control</p>
```

## Hierarquia de Configurações

O sistema segue uma hierarquia para determinar qual configuração usar:

1. **Projeto Específico** (mais prioritário)
2. **Instituição Específica**
3. **Configuração Global** (menos prioritário)

## Visualizando Notificações

### Notificações no Banco de Dados

1. Clique no ícone de sino no menu superior
2. Visualize as notificações não lidas
3. Clique em uma notificação para marcá-la como lida

### Notificações por Email

As notificações por email são enviadas automaticamente para os endereços configurados nos perfis dos usuários.

## Dicas de Uso

### 1. Teste as Configurações
Após criar uma configuração, teste-a para garantir que está funcionando corretamente.

### 2. Use Templates Consistentes
Mantenha um padrão visual nos templates de email para uma melhor experiência do usuário.

### 3. Configure por Projeto
Para projetos com necessidades específicas, crie configurações específicas ao invés de usar configurações globais.

### 4. Monitore os Destinatários
Certifique-se de que os usuários corretos estão recebendo as notificações adequadas.

## Solução de Problemas

### Notificações Não Estão Sendo Enviadas

1. Verifique se a configuração está **habilitada**
2. Confirme se o usuário tem o role correto
3. Verifique se o template de email existe (para notificações por email)
4. Consulte os logs do sistema para erros

### Emails Não Chegam

1. Verifique a configuração SMTP do sistema
2. Confirme se o endereço de email do usuário está correto
3. Verifique a pasta de spam
4. Consulte os logs de email

## Suporte

Para dúvidas ou problemas com o sistema de notificações, entre em contato com o administrador do sistema.