# 🧪 Guia de Teste de E-mails - Sistema de Controle de Bolsistas

## 📧 Configuração de E-mail para Desenvolvimento

### Opção 1: Log (Padrão - Recomendado)
Os e-mails são salvos em `storage/logs/laravel.log` sem enviar realmente.

```bash
# No arquivo .env
MAIL_MAILER=log
```

**Vantagens:**
- ✅ Não precisa de configuração externa
- ✅ E-mails ficam salvos localmente
- ✅ Rápido e confiável
- ✅ Não há risco de enviar e-mails acidentalmente

### Opção 2: Mailtrap (Recomendado para Testes Visuais)
Serviço gratuito para testar e-mails visualmente.

1. Acesse [mailtrap.io](https://mailtrap.io) e crie uma conta gratuita
2. Vá para "Inboxes" > "SMTP Settings"
3. Copie as credenciais SMTP

```bash
# No arquivo .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_password
MAIL_ENCRYPTION=null
```

### Opção 3: Gmail (Para Desenvolvimento Avançado)
Use senhas de app do Gmail.

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_app_password
MAIL_ENCRYPTION=tls
```

## 🛠️ Como Testar E-mails

### Comando Artisan de Teste
Criamos um comando personalizado para facilitar os testes:

```bash
# Teste básico
php artisan test:email usuario@exemplo.com

# Teste com senha temporária
php artisan test:email usuario@exemplo.com --with-password

# Teste interativo (pergunta o e-mail)
php artisan test:email
```

### Verificar E-mails no Log
Quando usar `MAIL_MAILER=log`:

```bash
# Ver últimas linhas do log
tail -f storage/logs/laravel.log

# Ou no PowerShell
Get-Content storage/logs/laravel.log -Tail 20 -Wait
```

### Testar Funcionalidades Específicas

#### 1. Notificação de Novo Usuário
```php
// No UserController@store, após criar usuário
$user->notify(new \App\Notifications\UserCreatedNotification($temporaryPassword));
```

#### 2. Reset de Senha
1. Acesse `/forgot-password`
2. Digite um e-mail
3. Verifique o log ou Mailtrap

#### 3. Alteração de Senha
1. Faça login
2. Vá para `/profile`
3. Clique em "Atualizar Senha"

## 📋 Lista de Verificação para E-mails

- [ ] Driver configurado corretamente (.env)
- [ ] Comando `test:email` funciona
- [ ] Notificação de usuário novo funciona
- [ ] Reset de senha funciona
- [ ] Alteração de senha funciona
- [ ] Templates de e-mail estão corretos
- [ ] Links nos e-mails funcionam

## 🔧 Troubleshooting

### E-mail não chega?
1. Verifique configuração em `.env`
2. Execute `php artisan config:clear`
3. Teste com `php artisan test:email`

### Erro de rota?
1. Execute `php artisan route:clear`
2. Verifique se `routes/auth.php` está incluído

### Problemas de encoding?
1. Use UTF-8 nos templates
2. Configure `MAIL_FROM_NAME` corretamente

## 📚 Recursos Adicionais

- [Documentação Laravel Mail](https://laravel.com/docs/mail)
- [Mailtrap](https://mailtrap.io)
- [SendGrid](https://sendgrid.com)
- [Amazon SES](https://aws.amazon.com/ses/)

---

**💡 Dica:** Sempre teste os e-mails antes de colocar em produção!