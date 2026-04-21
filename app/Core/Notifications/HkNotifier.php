<?php

namespace App\Core\Notifications;

use App\Core\Email\HkMail;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Application-wide notification dispatcher.
 *
 * Picks channels by intersecting:
 *  - the event's allowed channels (registry),
 *  - the channel's enabled flag (`notification_channels` table),
 *  - the recipient's per-event opt-in (`notification_preferences`).
 *
 * Mail is delegated to {@see HkMail} so admins can edit templates.
 * Database channel writes a row to Laravel's `notifications` table.
 */
class HkNotifier
{
    public function __construct(
        protected NotificationRegistry $registry,
        protected HkMail $mail,
    ) {}

    /**
     * @param  array<string, mixed>  $vars
     */
    public function notify(User $user, string $eventKey, array $vars = [], ?string $emailTemplateKey = null): void
    {
        $event = $this->registry->get($eventKey);
        if (! $event) {
            return;
        }

        foreach ($this->resolveChannels($user, $eventKey, $event['channels']) as $channel) {
            $this->dispatch($channel, $user, $eventKey, $vars, $emailTemplateKey);
        }
    }

    /**
     * @param  array<int, string>  $allowed
     * @return array<int, string>
     */
    protected function resolveChannels(User $user, string $eventKey, array $allowed): array
    {
        $enabled = NotificationChannel::where('is_enabled', true)->pluck('key')->all();
        $allowed = array_values(array_intersect($allowed, $enabled));

        if ($allowed === []) {
            return [];
        }

        $prefs = NotificationPreference::where('user_id', $user->id)
            ->where('event_key', $eventKey)
            ->whereIn('channel', $allowed)
            ->pluck('opted_in', 'channel')
            ->all();

        return array_values(array_filter($allowed, fn (string $c): bool => $prefs[$c] ?? true));
    }

    /**
     * @param  array<string, mixed>  $vars
     */
    protected function dispatch(string $channel, User $user, string $eventKey, array $vars, ?string $emailTemplateKey): void
    {
        match ($channel) {
            'mail' => $this->mail->sendTo($user->email, $emailTemplateKey ?? $eventKey, $vars + ['user' => $user->only(['name', 'email'])]),
            'database' => DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => $eventKey,
                'notifiable_type' => $user::class,
                'notifiable_id' => $user->id,
                'data' => json_encode($vars),
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            default => null, // sms / webpush are pluggable; no-op until a driver is registered.
        };
    }
}
