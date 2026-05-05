<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'payment_status_changed',
                'name' => 'Mudança de Status de Pagamento',
                'description' => 'Template para notificações de mudança de status de pagamentos',
                'subject' => 'Status do Pagamento Alterado - {{period}}',
                'body_html' => '
                    <h2>Olá {{scholarship_holder_name}}!</h2>
                    <p>O status do seu pagamento para o período <strong>{{period}}</strong> foi alterado.</p>
                    <p><strong>Valor:</strong> R$ {{amount}}</p>
                    <p><strong>Status anterior:</strong> {{old_status}}</p>
                    <p><strong>Novo status:</strong> {{new_status}}</p>
                    <p>Para mais detalhes, acesse o sistema.</p>
                    <p>Atenciosamente,<br>Equipe de Controle de Bolsistas</p>
                ',
                'body_text' => 'Olá {{scholarship_holder_name}}!

O status do seu pagamento para o período {{period}} foi alterado.

Valor: R$ {{amount}}
Status anterior: {{old_status}}
Novo status: {{new_status}}

Para mais detalhes, acesse o sistema.

Atenciosamente,
Equipe de Controle de Bolsistas',
                'variables' => [
                    'scholarship_holder_name',
                    'period',
                    'amount',
                    'old_status',
                    'new_status',
                ],
                'active' => true,
            ],
            [
                'key' => 'submission_submitted',
                'name' => 'Submissão de Frequência Enviada',
                'description' => 'Template para notificações de submissão de frequência enviada',
                'subject' => 'Nova Submissão de Frequência - {{month}}/{{year}}',
                'body_html' => '
                    <h2>Olá!</h2>
                    <p>O bolsista <strong>{{scholarship_holder_name}}</strong> enviou uma submissão de frequência para o período <strong>{{month}}/{{year}}</strong>.</p>
                    <p><strong>Total de horas:</strong> {{total_hours}}</p>
                    <p>Por favor, acesse o sistema para homologar a submissão.</p>
                    <p>Atenciosamente,<br>Sistema de Controle de Bolsistas</p>
                ',
                'body_text' => 'Olá!

O bolsista {{scholarship_holder_name}} enviou uma submissão de frequência para o período {{month}}/{{year}}.

Total de horas: {{total_hours}}

Por favor, acesse o sistema para homologar a submissão.

Atenciosamente,
Sistema de Controle de Bolsistas',
                'variables' => [
                    'scholarship_holder_name',
                    'month',
                    'year',
                    'total_hours',
                ],
                'active' => true,
            ],
            [
                'key' => 'submission_approved',
                'name' => 'Submissão de Frequência Aprovada',
                'description' => 'Template para notificações de submissão de frequência aprovada',
                'subject' => 'Submissão de Frequência Aprovada - {{month}}/{{year}}',
                'body_html' => '
                    <h2>Olá {{scholarship_holder_name}}!</h2>
                    <p>Sua submissão de frequência para o período <strong>{{month}}/{{year}}</strong> foi <span style="color: green;">aprovada</span>.</p>
                    <p><strong>Total de horas:</strong> {{total_hours}}</p>
                    <p>Para mais detalhes, acesse o sistema.</p>
                    <p>Atenciosamente,<br>Equipe de Controle de Bolsistas</p>
                ',
                'body_text' => 'Olá {{scholarship_holder_name}}!

Sua submissão de frequência para o período {{month}}/{{year}} foi aprovada.

Total de horas: {{total_hours}}

Para mais detalhes, acesse o sistema.

Atenciosamente,
Equipe de Controle de Bolsistas',
                'variables' => [
                    'scholarship_holder_name',
                    'month',
                    'year',
                    'total_hours',
                ],
                'active' => true,
            ],
            [
                'key' => 'submission_rejected',
                'name' => 'Submissão de Frequência Rejeitada',
                'description' => 'Template para notificações de submissão de frequência rejeitada',
                'subject' => 'Submissão de Frequência Rejeitada - {{month}}/{{year}}',
                'body_html' => '
                    <h2>Olá {{scholarship_holder_name}}!</h2>
                    <p>Sua submissão de frequência para o período <strong>{{month}}/{{year}}</strong> foi <span style="color: red;">rejeitada</span>.</p>
                    <p><strong>Motivo:</strong> {{reason}}</p>
                    <p>Os registros foram devolvidos para correção. Por favor, acesse o sistema para fazer as correções necessárias.</p>
                    <p>Atenciosamente,<br>Equipe de Controle de Bolsistas</p>
                ',
                'body_text' => 'Olá {{scholarship_holder_name}}!

Sua submissão de frequência para o período {{month}}/{{year}} foi rejeitada.

Motivo: {{reason}}

Os registros foram devolvidos para correção. Por favor, acesse o sistema para fazer as correções necessárias.

Atenciosamente,
Equipe de Controle de Bolsistas',
                'variables' => [
                    'scholarship_holder_name',
                    'month',
                    'year',
                    'reason',
                ],
                'active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
