<?php

namespace App\Providers;

use App\Core\Email\EmailTemplateRegistry;
use App\Core\Notifications\NotificationRegistry;
use App\Core\Settings\SettingsRepository;
use App\Listeners\LogAuthEvents;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EmailTemplateRegistry::class);
        $this->app->singleton(NotificationRegistry::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiters();

        if (config('csp.nonce_enabled', true)) {
            Vite::useCspNonce(app('csp-nonce'));
        }

        Event::subscribe(LogAuthEvents::class);
        $this->registerEmailTemplates();
        $this->registerNotificationEvents();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Register named rate limiters used across the app and admin.
     *
     * Limits are read from SettingsRepository (DB-backed) with config
     * fallback so admins can change them without redeploying.
     */
    protected function configureRateLimiters(): void
    {
        foreach (['auth', 'api', 'public_forms'] as $group) {
            $name = str_replace('_', '-', $group);
            RateLimiter::for("hk-{$name}", function (Request $request) use ($group) {
                [$max, $minutes] = $this->parseLimit($group);
                $key = ($request->user()?->id ?? $request->ip()).'|'.$group;

                return Limit::perMinutes($minutes, $max)->by($key);
            });
        }
    }

    /**
     * Parse a `max,minutes` setting string (e.g. "5,1") into a tuple.
     *
     * @return array{0:int,1:int}
     */
    protected function parseLimit(string $group): array
    {
        $value = (string) app(SettingsRepository::class)->get(
            "security.rate_limits.{$group}",
            config("hk.security.rate_limits.{$group}", '60,1'),
        );

        $parts = array_map('trim', explode(',', $value));
        $max = (int) ($parts[0] ?? 60);
        $minutes = (int) ($parts[1] ?? 1);

        return [max(1, $max), max(1, $minutes)];
    }

    /**
     * Pre-register the built-in email templates so the admin UI shows
     * them immediately on a clean install — even before they're seeded.
     */
    protected function registerEmailTemplates(): void
    {
        $registry = $this->app->make(EmailTemplateRegistry::class);

        $registry->register('fortify.verify_email', 'Verify email address', ['user.name', 'url'], 'Sent when a new account needs to confirm their email.');
        $registry->register('fortify.reset_password', 'Reset password', ['user.name', 'url'], 'Sent when a user requests a password reset.');
        $registry->register('fortify.welcome', 'Welcome', ['user.name'], 'Sent after registration completes.');
        $registry->register('account.password_changed', 'Password changed notice', ['user.name'], 'Security notice when a password is changed.');
        $registry->register('account.profile_deleted', 'Account deletion confirmation', ['user.name'], 'Confirmation email for GDPR delete.');
        $registry->register('contact.received', 'Contact form received', ['name', 'email', 'message'], 'Internal notification for contact form submissions.');
        $registry->register('booking.confirmed', 'Booking confirmation', ['user.name', 'booking.code', 'booking.total'], 'Sent when a booking is confirmed.');
        $registry->register('booking.cancelled', 'Booking cancellation', ['user.name', 'booking.code'], 'Sent when a booking is cancelled.');
    }

    /**
     * Pre-register the built-in notification events. Modules may add
     * more by resolving the registry from their own service providers.
     */
    protected function registerNotificationEvents(): void
    {
        $registry = $this->app->make(NotificationRegistry::class);

        $registry->register('account.password_changed', 'Password changed', ['mail', 'database'], 'user', 'Security notice when a password is changed.');
        $registry->register('account.profile_updated', 'Profile updated', ['mail', 'database'], 'user', 'Confirmation when a user changes their profile.');
        $registry->register('booking.confirmed', 'Booking confirmed', ['mail', 'database', 'sms'], 'user');
        $registry->register('booking.cancelled', 'Booking cancelled', ['mail', 'database', 'sms'], 'user');
        $registry->register('admin.new_user', 'New user registered', ['mail', 'database'], 'admin');
        $registry->register('admin.new_booking', 'New booking received', ['mail', 'database'], 'admin');
        $registry->register('admin.contact_received', 'New contact form submission', ['mail', 'database'], 'admin');
    }
}
