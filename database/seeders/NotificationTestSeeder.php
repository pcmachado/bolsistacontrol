<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use App\Models\User;

class NotificationTestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            echo "⚠ Nenhum usuário encontrado. Notifications não geradas.\n";
            return;
        }

        foreach ($users as $user) {

            // 🔹 3 notificações não lidas
            for ($i = 1; $i <= 3; $i++) {
                DatabaseNotification::create([
                    'id'              => Str::uuid()->toString(),
                    'type'            => 'App\Notifications\GenericNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => [
                        'title'   => "Notificação pendente {$i}",
                        'message' => "Olá {$user->name}, esta é uma notificação de teste número {$i}.",
                    ],
                    'created_at'      => now()->subDays(rand(1, 10)),
                ]);
            }

            // 🔹 2 notificações já lidas
            for ($i = 1; $i <= 2; $i++) {
                DatabaseNotification::create([
                    'id'              => Str::uuid()->toString(),
                    'type'            => 'App\Notifications\GenericNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => [
                        'title'   => "Notificação lida {$i}",
                        'message' => "Esta notificação já foi marcada como lida.",
                    ],
                    'read_at'         => now()->subDays(rand(5, 15)),
                    'created_at'      => now()->subDays(rand(10, 20)),
                ]);
            }
        }

        echo "✔ Notificações de teste criadas com sucesso!\n";
    }
}
