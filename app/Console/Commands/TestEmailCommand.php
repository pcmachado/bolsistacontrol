<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?} {--with-password : Gerar senha temporária}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testar envio de e-mails no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $withPassword = $this->option('with-password');

        if (! $email) {
            $email = $this->ask('Digite o e-mail para teste');
        }

        $this->info('🔍 Verificando configuração de e-mail...');

        // Verificar configuração atual
        $mailer = config('mail.default');
        $this->info("📧 Mailer atual: {$mailer}");

        // Criar usuário de teste ou usar existente
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->info('👤 Criando usuário de teste...');
            $user = User::create([
                'name' => 'Usuário de Teste',
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
        }

        $temporaryPassword = null;
        if ($withPassword) {
            $temporaryPassword = Str::random(12);
            $this->info("🔑 Senha temporária gerada: {$temporaryPassword}");
        }

        $this->info('📤 Enviando e-mail de teste...');

        try {
            Mail::to($email)->send(new TestEmail($user, $temporaryPassword));

            $this->info('✅ E-mail enviado com sucesso!');

            if ($mailer === 'log') {
                $this->info('📝 Verifique os logs em: storage/logs/laravel.log');
                $this->newLine();
                $this->comment('💡 Para ver o conteúdo do e-mail, execute:');
                $this->comment('   tail -f storage/logs/laravel.log');
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar e-mail: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
