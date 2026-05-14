<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'event_type' => 'payment_sent_to_financial',
                'notification_type' => 'database',
                'recipients' => [
                    ['type' => 'financial_coordinator'],
                ],
                'enabled' => true,
            ],
            [
                'event_type' => 'payment_status_changed',
                'notification_type' => 'database',
                'recipients' => [
                    ['type' => 'financial_coordinator'],
                ],
                'enabled' => true,
            ],
            [
                'event_type' => 'submission_submitted',
                'notification_type' => 'database',
                'recipients' => [
                    ['type' => 'unit_coordinator'],
                    ['type' => 'administrative_general_coordinator'],
                ],
                'enabled' => true,
            ],
            [
                'event_type' => 'submission_approved',
                'notification_type' => 'database',
                'recipients' => [
                    ['type' => 'submission_owner'],
                ],
                'enabled' => true,
            ],
            [
                'event_type' => 'submission_rejected',
                'notification_type' => 'database',
                'recipients' => [
                    ['type' => 'submission_owner'],
                ],
                'enabled' => true,
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                [
                    'event_type' => $setting['event_type'],
                    'project_id' => null,
                    'institution_id' => null,
                ],
                $setting
            );
        }
    }
}
