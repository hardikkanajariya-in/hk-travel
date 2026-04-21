<?php

namespace Database\Seeders;

use App\Models\NotificationChannel;
use Illuminate\Database\Seeder;

class NotificationChannelsSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['key' => 'mail', 'label' => 'Email', 'description' => 'Transactional email through the configured mailer.', 'is_enabled' => true],
            ['key' => 'database', 'label' => 'In-app', 'description' => 'Bell-icon notifications inside the dashboard.', 'is_enabled' => true],
            ['key' => 'sms', 'label' => 'SMS', 'description' => 'Text message via configured SMS driver.', 'is_enabled' => false],
            ['key' => 'webpush', 'label' => 'Web push', 'description' => 'Browser push notifications.', 'is_enabled' => false],
        ];

        foreach ($channels as $row) {
            NotificationChannel::firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
