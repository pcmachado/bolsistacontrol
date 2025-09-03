<?php

// app/Console/Commands/CheckMissingFrequency.php
namespace App\Console\Commands;

use App\Models\Bolsista;
use App\Models\Notificacao;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckMissingFrequency extends Command
{
    /**
     * O nome e a assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'check:missing-frequency';
    
    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Verifica se os bolsistas registraram a frequência do mês anterior e cria notificações na tela.';

    public function handle(): void
    {
        $this->info('Iniciando a verificação de frequência...');
        // Obter o mês e ano do mês anterior
        $mesAnterior = Carbon::now()->subMonth();
        $mes = $mesAnterior->month;
        $ano = $mesAnterior->year;

        // Buscar todos os bolsistas
        $bolsistas = Bolsista::all();

        foreach ($bolsistas as $bolsista) {
            // Verificar se o bolsista tem registros para o mês anterior
            $registrosDoMes = $bolsista->registros()->whereMonth('data', $mes)->whereYear('data', $ano)->count();

            // Lógica simples: se não houver registros, cria a notificação
            if ($registrosDoMes === 0) {
                $this->warn("Frequência faltando para o bolsista: {$bolsista->nome}");

                // Cria a notificação no banco de dados em vez de enviar um e-mail
                $nomeMes = Carbon::create()->month($mes)->locale('pt_BR')->monthName;
                $mensagem = "Seu registro de frequência para o mês de **" . ucwords($nomeMes) . " de {$ano}** pode estar incompleto. Por favor, regularize sua situação.";
                
                Notificacao::create([
                    'bolsista_id' => $bolsista->id,
                    'mensagem' => $mensagem,
                ]);
            }
        }
        $this->info('Verificação de frequência concluída.');
    }
}