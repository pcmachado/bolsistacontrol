<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use App\Models\User;

class NotificationTestSeeder extends Seeder
{
    protected function sanitize($value): string
    {
        return preg_replace('/[\r\n]+/', ' ', $value);
    }

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            echo "⚠ Nenhum usuário encontrado. Notifications não geradas.\n";
            return;
        }

        foreach ($users as $user) {

            // ================================
            // 🔹 NÃO LIDAS (cenários reais)
            // ================================
            $pendingNotifications = [
                [
                    'title' => 'Pagamento disponível',
                    'message' => "Seu pagamento foi enviado para execução.",
                    'level' => 'info',
                    'url' => route('payments.my'),
                ],
                [
                    'title' => 'Frequência pendente',
                    'message' => "Você possui registros não submetidos neste mês.",
                    'level' => 'warning',
                    'url' => route('attendance.index'),
                ],
                [
                    'title' => 'Ação necessária',
                    'message' => "Seu relatório foi rejeitado e precisa de ajustes.",
                    'level' => 'danger',
                    'url' => route('attendance.submissions.my'),
                ],
            ];

            foreach ($pendingNotifications as $data) {
                DatabaseNotification::create([
                    'id'              => Str::uuid()->toString(),
                    'type'            => 'App\Notifications\IntelligentSystemAlert',
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => [
                        'title'   => $this->sanitize($data['title']),
                        'message' => $this->sanitize($data['message']),
                        'level'   => $data['level'],
                        'url'     => $data['url'],
                    ],
                    'created_at'      => now()->subDays(rand(1, 5)),
                ]);
            }

            // ================================
            // 🔹 LIDAS
            // ================================
            $readNotifications = [
                [
                    'title' => 'Pagamento realizado',
                    'message' => "Seu pagamento foi concluído com sucesso.",
                    'level' => 'success',
                    'url' => route('payments.my'),
                ],
                [
                    'title' => 'Frequência homologada',
                    'message' => "Seu relatório mensal foi aprovado.",
                    'level' => 'success',
                    'url' => route('attendance.reports.index'),
                ],
            ];

            foreach ($readNotifications as $data) {
                DatabaseNotification::create([
                    'id'              => Str::uuid()->toString(),
                    'type'            => 'App\Notifications\IntelligentSystemAlert',
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => [
                        'title'   => $this->sanitize($data['title']),
                        'message' => $this->sanitize($data['message']),
                        'level'   => $data['level'],
                        'url'     => $data['url'],
                    ],
                    'read_at'         => now()->subDays(rand(2, 10)),
                    'created_at'      => now()->subDays(rand(10, 20)),
                ]);
            }
        }

        echo "✔ Notificações realistas criadas com sucesso!\n";
    }
}