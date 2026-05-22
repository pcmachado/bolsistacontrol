{{-- E-mail de teste --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de E-mail</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #343a40; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .highlight { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Teste de E-mail</h1>
            <p>ProBolsas - Sistema de Gestão de Bolsas, Frequência e Pagamentos Acadêmicos</p>
        </div>

        <div class="content">
            <h2>Olá, {{ $user->name }}!</h2>

            <p>Este é um e-mail de teste enviado do sistema Laravel.</p>

            <div class="highlight">
                <strong>Informações do usuário:</strong><br>
                Nome: {{ $user->name }}<br>
                E-mail: {{ $user->email }}<br>
                @if($temporaryPassword)
                Senha temporária: <code>{{ $temporaryPassword }}</code>
                @endif
            </div>

            <p>Se você recebeu este e-mail, significa que o sistema de e-mail está funcionando corretamente!</p>

            <p>
                <a href="{{ url('/login') }}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Acessar Sistema
                </a>
            </p>
        </div>

        <div class="footer">
            <p>Este é um e-mail automático. Não responda.</p>
            <p>&copy; 2026 ProBolsas - Sistema de Gestão Integrada de Bolsistas, Frequências e Pagamentos</p>
        </div>
    </div>
</body>
</html>