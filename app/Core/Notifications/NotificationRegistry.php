<?php

namespace App\Core\Notifications;

/**
 * Catalog of notification events. Modules register events here so the
 * admin UI can show a matrix of [event x channel] toggles, and so users
 * can opt in/out per channel.
 */
class NotificationRegistry
{
    /** @var array<string, array{label: string, description?: string, channels: array<int, string>, audience: string}> */
    protected array $events = [];

    /**
     * @param  array<int, string>  $channels  Allowed channel keys (mail, database, sms, webpush).
     * @param  string  $audience  Either "user" or "admin".
     */
    public function register(string $key, string $label, array $channels, string $audience = 'user', ?string $description = null): void
    {
        $this->events[$key] = [
            'label' => $label,
            'description' => $description,
            'channels' => $channels,
            'audience' => $audience,
        ];
    }

    /** @return array<string, array{label: string, description?: string, channels: array<int, string>, audience: string}> */
    public function all(): array
    {
        return $this->events;
    }

    /** @return array{label: string, description?: string, channels: array<int, string>, audience: string}|null */
    public function get(string $key): ?array
    {
        return $this->events[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->events[$key]);
    }
}
