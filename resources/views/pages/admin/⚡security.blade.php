<?php

use App\Concerns\SettingsForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security')] #[Layout('components.layouts.admin')] class extends Component {
    use SettingsForm;

    public function mount(): void
    {
        $this->state = [
            'force_https' => false,
            'csp_enabled' => true,
            'frame_deny' => true,
            'nosniff' => true,
            'coop' => true,
            'referrer_policy' => 'strict-origin-when-cross-origin',
            'permissions_policy' => 'camera=(), microphone=(), geolocation=(self), payment=(self)',
            'hsts_enabled' => false,
            'hsts_max_age' => 31536000,
            'hsts_include_subdomains' => true,
            'hsts_preload' => false,
            'rate_auth' => '5,1',
            'rate_api' => '60,1',
            'rate_public_forms' => '10,1',
            'honeypot_enabled' => true,
        ];

        $this->loadSettings();
    }

    public function save(): void
    {
        $this->saveSettings();
    }

    /** @return array<string, string> */
    protected function settingsKeys(): array
    {
        return [
            'force_https' => 'security.force_https',
            'csp_enabled' => 'security.csp.enabled',
            'frame_deny' => 'security.headers.frame_deny',
            'nosniff' => 'security.headers.nosniff',
            'coop' => 'security.headers.coop',
            'referrer_policy' => 'security.headers.referrer_policy',
            'permissions_policy' => 'security.headers.permissions_policy',
            'hsts_enabled' => 'security.hsts.enabled',
            'hsts_max_age' => 'security.hsts.max_age',
            'hsts_include_subdomains' => 'security.hsts.include_subdomains',
            'hsts_preload' => 'security.hsts.preload',
            'rate_auth' => 'security.rate_limits.auth',
            'rate_api' => 'security.rate_limits.api',
            'rate_public_forms' => 'security.rate_limits.public_forms',
            'honeypot_enabled' => 'security.honeypot.enabled',
        ];
    }

    /** @return array<string, mixed> */
    protected function settingsRules(): array
    {
        $rateRule = ['required', 'string', 'regex:/^\d+,\d+$/'];

        return [
            'state.force_https' => 'boolean',
            'state.csp_enabled' => 'boolean',
            'state.frame_deny' => 'boolean',
            'state.nosniff' => 'boolean',
            'state.coop' => 'boolean',
            'state.referrer_policy' => 'required|string|max:64',
            'state.permissions_policy' => 'nullable|string|max:1024',
            'state.hsts_enabled' => 'boolean',
            'state.hsts_max_age' => 'integer|min:0|max:63072000',
            'state.hsts_include_subdomains' => 'boolean',
            'state.hsts_preload' => 'boolean',
            'state.rate_auth' => $rateRule,
            'state.rate_api' => $rateRule,
            'state.rate_public_forms' => $rateRule,
            'state.honeypot_enabled' => 'boolean',
        ];
    }
}; ?>

<div>
    <x-admin.page-header
        title="Security"
        description="HTTPS, CSP, security headers, rate limits and honeypot configuration." />

    <x-admin.flash />

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Transport &amp; HTTPS</h2>
            <div class="space-y-4">
                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.force_https" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">Force HTTPS</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Permanently redirect plain HTTP to HTTPS in production. Disabled automatically in local/testing environments.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.hsts_enabled" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">Enable HSTS (Strict-Transport-Security)</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Tell browsers to only contact this site over HTTPS for the duration below.</span>
                    </span>
                </label>

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-ui.input wire:model="state.hsts_max_age" name="hsts_max_age" type="number" label="HSTS max-age (seconds)" hint="31536000 = 1 year" />
                    <label class="flex items-center gap-2 self-end pb-2 text-sm">
                        <input type="checkbox" wire:model="state.hsts_include_subdomains" class="size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                        Include subdomains
                    </label>
                    <label class="flex items-center gap-2 self-end pb-2 text-sm">
                        <input type="checkbox" wire:model="state.hsts_preload" class="size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                        Preload list eligible
                    </label>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Headers</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.csp_enabled" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">Content Security Policy</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Restrict where scripts, styles and frames can load from.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.frame_deny" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">X-Frame-Options: DENY</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Block all framing of this site (clickjacking protection).</span>
                    </span>
                </label>
                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.nosniff" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">X-Content-Type-Options: nosniff</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Stop browsers from MIME-sniffing responses.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3">
                    <input type="checkbox" wire:model="state.coop" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                    <span>
                        <span class="block text-sm font-medium">Cross-Origin-Opener-Policy</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Isolate browsing context from cross-origin windows.</span>
                    </span>
                </label>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <x-ui.input wire:model="state.referrer_policy" name="referrer_policy" label="Referrer-Policy" />
                <x-ui.input wire:model="state.permissions_policy" name="permissions_policy" label="Permissions-Policy" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-1">Rate limits</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Format: <code>requests,minutes</code> &mdash; e.g. <code>5,1</code> = 5 requests per minute.</p>

            <div class="grid gap-4 md:grid-cols-3">
                <x-ui.input wire:model="state.rate_auth" name="rate_auth" label="Auth (login, register, reset)" />
                <x-ui.input wire:model="state.rate_api" name="rate_api" label="API endpoints" />
                <x-ui.input wire:model="state.rate_public_forms" name="rate_public_forms" label="Public forms" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold mb-4">Honeypot</h2>
            <label class="flex items-start gap-3">
                <input type="checkbox" wire:model="state.honeypot_enabled" class="mt-1 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500" />
                <span>
                    <span class="block text-sm font-medium">Enable honeypot anti-spam on public forms</span>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">Hidden form fields catch automated submissions. Combine with captcha for best results.</span>
                </span>
            </label>
        </x-ui.card>

        <div class="flex justify-end">
            <x-ui.button type="submit">Save changes</x-ui.button>
        </div>
    </form>
</div>

