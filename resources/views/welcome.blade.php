<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Sistema de Bolsistas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white flex items-center justify-center min-h-screen">
    <div class="text-center p-8 bg-white/20 backdrop-blur-lg rounded-xl shadow-2xl max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold mb-4 animate-fade-in-up">
            Sistema de Gerenciamento de Frequência de Bolsistas
        </h1>
        <p class="text-lg font-light mb-8 animate-fade-in">
            Acompanhe a frequência, gere relatórios e gerencie o seu tempo de forma eficiente.
        </p>
        <div class="space-x-4">
            <a href="{{ route('attendance.create') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-full font-bold shadow-lg transform hover:scale-105 transition duration-300">
                Registrar Frequência
            </a>
            <a href="{{ route('notifications.index') }}" class="inline-block px-8 py-3 bg-indigo-700 text-white rounded-full font-bold shadow-lg transform hover:scale-105 transition duration-300">
                Ver Notificações
            </a>
        </div>
    </div>
</body>
</html>