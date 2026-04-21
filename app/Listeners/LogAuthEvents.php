<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Listen to Laravel auth events and log them to the activity log
 * under the `auth` log name. Captures IP and user agent so admins
 * can spot suspicious patterns.
 */
class LogAuthEvents
{
    public function __construct(protected Request $request) {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            Lockout::class => 'handleLockout',
            PasswordReset::class => 'handlePasswordReset',
            Registered::class => 'handleRegistered',
        ];
    }

    public function handleLogin(Login $event): void
    {
        $this->log('login', $event->user, ['guard' => $event->guard]);
    }

    public function handleLogout(Logout $event): void
    {
        $this->log('logout', $event->user, ['guard' => $event->guard]);
    }

    public function handleFailed(Failed $event): void
    {
        $this->log('login_failed', $event->user, [
            'guard' => $event->guard,
            'credentials' => ['email' => $event->credentials['email'] ?? null],
        ]);
    }

    public function handleLockout(Lockout $event): void
    {
        $this->log('lockout', null, [
            'email' => $event->request->input('email'),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->log('password_reset', $event->user);
    }

    public function handleRegistered(Registered $event): void
    {
        $this->log('registered', $event->user);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    protected function log(string $event, mixed $user = null, array $properties = []): void
    {
        $properties = array_merge($properties, [
            'ip' => $this->request->ip(),
            'user_agent' => substr((string) $this->request->userAgent(), 0, 255),
        ]);

        $logger = activity('auth')->withProperties($properties)->event($event);

        if ($user) {
            $logger = $logger->causedBy($user)->performedOn($user);
        }

        $logger->log($event);
    }
}
