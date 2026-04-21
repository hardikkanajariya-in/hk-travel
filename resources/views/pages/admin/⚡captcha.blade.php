<?php

use App\Concerns\SettingsForm;
use App\Core\Captcha\CaptchaService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Captcha')] #[Layout('components.layouts.admin')] class extends Component {
    use SettingsForm;

    /** @var array<int, string> */
    public array $availableForms = ['login', 'register', 'password.reset', 'contact', 'inquiry', 'comment', 'review'];

    /** @var array<int, array<string, string>> */
    public array $availableDrivers = [
        ['key' => 'turnstile', 'label' => 'Cloudflare Turnstile', 'note' => 'Free, privacy-friendly. Recommended.'],
        ['key' => 'hcaptcha', 'label' => 'hCaptcha', 'note' => 'Privacy-focused alternative to reCAPTCHA.'],
        ['key' => 'recaptcha', 'label' => 'Google reCAPTCHA v3', 'note' => 'Score-based invisible captcha.'],
    ];

    public ?string $testResult = null;

    public function mount(): void
    {
        $this->state = [
            'enabled' => false,
            'driver' => 'turnstile',
            'protect' => ['login', 'register', 'password.reset'],
            'turnstile_site' => '',
            'turnstile_secret' => '',
            'hcaptcha_site' => '',
            'hcaptcha_secret' => '',
            'recaptcha_site' => '',
            'recaptcha_secret' => '',
            'recaptcha_threshold' => 0.5,
        ];

        $this->loadSettings();
    }

    public function save(): void
    {
        $this->saveSettings();
        $this->testResult = null;
    }

    public function testKeys(): void
    {
        // Save first so the service uses the just-entered values.
        $this->saveSettings();

        $service = app(CaptchaService::class);
        $driver = $service->driver();

        if ($driver === null) {
            $this->testResult = 'error:'.__('Selected driver has no site/secret keys configured.');

            return;
        }

        // Verifying with an obviously-invalid token should return false
        // gracefully (= keys reach the provider). Real verification needs
        // a real client-side challenge, which we can't run from the admin.
        $accepted = ! $driver->verify('hk-test-token-'.uniqid(), '127.0.0.1');

        $this->testResult = $accepted
            ? 'ok:'.__('Driver reachable. Keys appear valid (a real challenge token is required for full verification).')
            : 'warn:'.__('Driver responded but rejected the test token, which is expected. Keys look reachable.');
    }

    /** @return array<string, string> */
    protected function settingsKeys(): array
    {
        return [
            'enabled' => 'captcha.enabled',
            'driver' => 'captcha.driver',
            'protect' => 'captcha.protect',
            'turnstile_site' => 'captcha.drivers.turnstile.site_key',
            'turnstile_secret' => 'captcha.drivers.turnstile.secret_key',
            'hcaptcha_site' => 'captcha.drivers.hcaptcha.site_key',
            'hcaptcha_secret' => 'captcha.drivers.hcaptcha.secret_key',
            'recaptcha_site' => 'captcha.drivers.recaptcha.site_key',
            'recaptcha_secret' => 'captcha.drivers.recaptcha.secret_key',
            'recaptcha_threshold' => 'captcha.drivers.recaptcha.threshold',
        ];
    }

    /** @return array<string, mixed> */
    protected function settingsRules(): array
    {
        return [
            'state.enabled' => 'boolean',
            'state.driver' => 'required|in:turnstile,hcaptcha,recaptcha',
            'state.protect' => 'array',
            'state.protect.*' => 'string|in:'.implode(',', $this->availableForms),
            'state.turnstile_site' => 'nullable|string|max:255',
            'state.turnstile_secret' => 'nullable|string|max:255',
            'state.hcaptcha_site' => 'nullable|string|max:255',
            'state.hcaptcha_secret' => 'nullable|string|max:255',
            'state.recaptcha_site' => 'nullable|string|max:255',
            'state.recaptcha_secret' => 'nullable|string|max:255',
            'state.recaptcha_threshold' => 'numeric|min:0|max:1',
        ];
    }
}; ?>

<div>
    <x-admin.page-header
        title="Captcha"
        description="Choose a provider, configure keys and pick which forms get protected." />

    <x-admin.flash />

    @if ($testResult)
        @php [$kind, $msg] = explode(':', $testResult, 2); @endphp
        <div class="mb-4 rounded-md border px-4 py-3 text-sm
            {{ $kind === 'ok' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200' : '' }}
            {{ $kind === 'warn' ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200' : '' }}
            {{ $kind === 'error' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200' : '' }}">
            {{ $msg }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Master switch</h2>

            <label class="flex items-start gap-3">
                <input type="checkbox" wire:model="state.enabled" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                <span>
                    <span class="block text-sm font-medium">Enable captcha protection</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">Off by default. When enabled, the active driver must have valid keys or the service silently falls back to off (no broken forms).</span>
                </span>
            </label>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Driver</h2>

            <div class="space-y-3">
                @foreach ($availableDrivers as $driver)
                    <label class="flex items-start gap-3 rounded-md border border-zinc-200 dark:border-zinc-800 p-3 cursor-pointer">
                        <input type="radio" wire:model="state.driver" value="{{ $driver['key'] }}" class="mt-1 size-4 border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                        <span>
                            <span class="block text-sm font-medium">{{ $driver['label'] }}</span>
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $driver['note'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Keys</h2>

            <div x-data="{ driver: @entangle('state.driver') }" class="space-y-6">
                <div x-show="driver === 'turnstile'" class="grid gap-4 md:grid-cols-2">
                    <x-ui.input wire:model="state.turnstile_site" name="turnstile_site" label="Turnstile site key" />
                    <x-ui.input wire:model="state.turnstile_secret" name="turnstile_secret" label="Turnstile secret key" type="password" />
                </div>

                <div x-show="driver === 'hcaptcha'" x-cloak class="grid gap-4 md:grid-cols-2">
                    <x-ui.input wire:model="state.hcaptcha_site" name="hcaptcha_site" label="hCaptcha site key" />
                    <x-ui.input wire:model="state.hcaptcha_secret" name="hcaptcha_secret" label="hCaptcha secret key" type="password" />
                </div>

                <div x-show="driver === 'recaptcha'" x-cloak class="grid gap-4 md:grid-cols-3">
                    <x-ui.input wire:model="state.recaptcha_site" name="recaptcha_site" label="reCAPTCHA site key" />
                    <x-ui.input wire:model="state.recaptcha_secret" name="recaptcha_secret" label="reCAPTCHA secret key" type="password" />
                    <x-ui.input wire:model="state.recaptcha_threshold" name="recaptcha_threshold" label="Score threshold" type="number" hint="0.0 – 1.0 (default 0.5)" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-1">Protected forms</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Tick the forms where the captcha widget should appear and verification should run.</p>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($availableForms as $form)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="state.protect" value="{{ $form }}" class="size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                        <code class="text-xs">{{ $form }}</code>
                    </label>
                @endforeach
            </div>
        </x-ui.card>

        <div class="flex justify-end gap-2">
            <x-ui.button type="button" variant="secondary" wire:click="testKeys">Test verification</x-ui.button>
            <x-ui.button type="submit">Save changes</x-ui.button>
        </div>
    </form>
</div>
