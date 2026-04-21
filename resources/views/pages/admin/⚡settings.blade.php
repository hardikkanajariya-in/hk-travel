<?php

use App\Concerns\SettingsForm;
use App\Core\Support\Choices;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Settings')] #[Layout('components.layouts.admin')] class extends Component {
    use SettingsForm;

    public function mount(): void
    {
        $this->state = [
            // General
            'site_name' => '',
            'tagline' => '',
            'default_locale' => 'en',
            'default_currency' => 'USD',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',

            // Contact
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
            'contact_hours' => '',
            'contact_map_embed' => '',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_twitter' => '',
            'social_youtube' => '',
            'social_linkedin' => '',
            'social_whatsapp' => '',

            // SEO defaults
            'seo_title_separator' => '·',
            'seo_meta_description' => '',
            'seo_og_image' => '',
            'seo_robots_default' => 'index, follow',
            'seo_canonical_host' => '',

            // Analytics
            'analytics_ga4' => '',
            'analytics_gtm' => '',
            'analytics_meta_pixel' => '',
            'analytics_hotjar' => '',
            'analytics_custom_head' => '',
            'analytics_custom_body' => '',

            // Cookie banner
            'cookie_enabled' => true,
            'cookie_message' => 'We use cookies to improve your experience. You can manage your preferences anytime.',
            'cookie_accept_label' => 'Accept all',
            'cookie_reject_label' => 'Reject',
            'cookie_settings_label' => 'Preferences',
            'cookie_policy_url' => '/privacy',
            'cookie_position' => 'bottom',
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
            // General
            'site_name' => 'brand.name',
            'tagline' => 'brand.tagline',
            'default_locale' => 'localization.default',
            'default_currency' => 'payments.default_currency',
            'timezone' => 'general.timezone',
            'date_format' => 'general.date_format',
            'time_format' => 'general.time_format',

            // Contact
            'contact_email' => 'contact.email',
            'contact_phone' => 'contact.phone',
            'contact_address' => 'contact.address',
            'contact_hours' => 'contact.hours',
            'contact_map_embed' => 'contact.map_embed',
            'social_facebook' => 'contact.social.facebook',
            'social_instagram' => 'contact.social.instagram',
            'social_twitter' => 'contact.social.twitter',
            'social_youtube' => 'contact.social.youtube',
            'social_linkedin' => 'contact.social.linkedin',
            'social_whatsapp' => 'contact.social.whatsapp',

            // SEO
            'seo_title_separator' => 'seo.site_title_separator',
            'seo_meta_description' => 'seo.meta_description',
            'seo_og_image' => 'seo.og_image',
            'seo_robots_default' => 'seo.robots_default',
            'seo_canonical_host' => 'seo.canonical_host',

            // Analytics
            'analytics_ga4' => 'seo.analytics.ga4',
            'analytics_gtm' => 'seo.analytics.gtm',
            'analytics_meta_pixel' => 'seo.analytics.meta_pixel',
            'analytics_hotjar' => 'seo.analytics.hotjar',
            'analytics_custom_head' => 'seo.analytics.custom_head',
            'analytics_custom_body' => 'seo.analytics.custom_body',

            // Cookie banner
            'cookie_enabled' => 'cookie.enabled',
            'cookie_message' => 'cookie.message',
            'cookie_accept_label' => 'cookie.accept_label',
            'cookie_reject_label' => 'cookie.reject_label',
            'cookie_settings_label' => 'cookie.settings_label',
            'cookie_policy_url' => 'cookie.policy_url',
            'cookie_position' => 'cookie.position',
        ];
    }

    /** @return array<string, mixed> */
    protected function settingsRules(): array
    {
        return [
            'state.site_name' => 'required|string|max:120',
            'state.tagline' => 'nullable|string|max:255',
            'state.default_locale' => 'required|string|max:8',
            'state.default_currency' => 'required|string|size:3',
            'state.timezone' => 'required|string|max:64',
            'state.date_format' => 'required|string|max:32',
            'state.time_format' => 'required|string|max:32',

            'state.contact_email' => 'nullable|email',
            'state.contact_phone' => 'nullable|string|max:64',
            'state.contact_address' => 'nullable|string|max:1000',
            'state.contact_hours' => 'nullable|string|max:255',
            'state.contact_map_embed' => 'nullable|string|max:5000',
            'state.social_facebook' => 'nullable|url|max:255',
            'state.social_instagram' => 'nullable|url|max:255',
            'state.social_twitter' => 'nullable|url|max:255',
            'state.social_youtube' => 'nullable|url|max:255',
            'state.social_linkedin' => 'nullable|url|max:255',
            'state.social_whatsapp' => 'nullable|string|max:64',

            'state.seo_title_separator' => 'required|string|max:8',
            'state.seo_meta_description' => 'nullable|string|max:255',
            'state.seo_og_image' => 'nullable|string|max:255',
            'state.seo_robots_default' => 'required|string|max:64',
            'state.seo_canonical_host' => 'nullable|string|max:255',

            'state.analytics_ga4' => 'nullable|string|max:32',
            'state.analytics_gtm' => 'nullable|string|max:32',
            'state.analytics_meta_pixel' => 'nullable|string|max:32',
            'state.analytics_hotjar' => 'nullable|string|max:32',
            'state.analytics_custom_head' => 'nullable|string|max:5000',
            'state.analytics_custom_body' => 'nullable|string|max:5000',

            'state.cookie_enabled' => 'boolean',
            'state.cookie_message' => 'required_if:state.cookie_enabled,true|nullable|string|max:1000',
            'state.cookie_accept_label' => 'required_if:state.cookie_enabled,true|nullable|string|max:64',
            'state.cookie_reject_label' => 'nullable|string|max:64',
            'state.cookie_settings_label' => 'nullable|string|max:64',
            'state.cookie_policy_url' => 'nullable|string|max:255',
            'state.cookie_position' => 'required|in:bottom,top,bottom-left,bottom-right',
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Settings" subtitle="Site identity, contact details, SEO, analytics, and cookie banner." />

    <x-admin.flash :message="session('settings.saved')" />

    <x-ui.tabs :tabs="[
        'general' => 'General',
        'contact' => 'Contact',
        'seo' => 'SEO',
        'analytics' => 'Analytics',
        'cookie' => 'Cookie banner',
    ]">
        <x-ui.tab-panel name="general">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Site identity</h2>
                <div class="space-y-4">
                    <x-ui.input wire:model="state.site_name" label="Site name" required />
                    <x-ui.input wire:model="state.tagline" label="Tagline" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Locale & formats</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.select wire:model="state.default_locale" label="Default language" required searchable
                                  :options="\App\Core\Support\Choices::locales()"
                                  hint="The language new visitors see by default." />
                    <x-ui.select wire:model="state.default_currency" label="Default currency" required searchable
                                  :options="\App\Core\Support\Choices::currencies()"
                                  hint="Used to display prices across the site." />
                    <x-ui.select wire:model="state.timezone" label="Timezone" required searchable
                                  :options="\App\Core\Support\Choices::timezones()"
                                  hint="All times shown on the site use this zone." />
                    <x-ui.select wire:model="state.date_format" label="Date format" required
                                  :options="\App\Core\Support\Choices::dateFormats()"
                                  hint="How dates appear to your visitors." />
                    <x-ui.select wire:model="state.time_format" label="Time format" required
                                  :options="\App\Core\Support\Choices::timeFormats()"
                                  hint="How clock times appear to your visitors." />
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="contact">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Contact information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input type="email" wire:model="state.contact_email" label="Email" />
                    <x-ui.input wire:model="state.contact_phone" label="Phone" />
                    <x-ui.input wire:model="state.contact_hours" label="Working hours" />
                    <x-ui.input wire:model="state.social_whatsapp" label="WhatsApp number" />
                </div>
                <div class="mt-4">
                    <x-ui.textarea wire:model="state.contact_address" label="Address" rows="3" />
                </div>
                <div class="mt-4">
                    <x-ui.textarea wire:model="state.contact_map_embed" label="Map embed (HTML iframe)" rows="3" hint="Will be sanitized server-side." />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Social links</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input wire:model="state.social_facebook" label="Facebook URL" />
                    <x-ui.input wire:model="state.social_instagram" label="Instagram URL" />
                    <x-ui.input wire:model="state.social_twitter" label="Twitter / X URL" />
                    <x-ui.input wire:model="state.social_youtube" label="YouTube URL" />
                    <x-ui.input wire:model="state.social_linkedin" label="LinkedIn URL" />
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="seo">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">SEO defaults</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.select wire:model="state.seo_title_separator" label="Title separator" required
                                  :options="\App\Core\Support\Choices::titleSeparators()"
                                  hint="Character between the page title and your site name in browser tabs." />
                    <x-ui.select wire:model="state.seo_robots_default" label="Search engine visibility" required
                                  :options="\App\Core\Support\Choices::robotsDirectives()"
                                  hint="Controls whether search engines like Google can list your pages." />
                    <x-ui.input wire:model="state.seo_canonical_host" label="Preferred website address"
                                  placeholder="https://www.example.com"
                                  hint="Optional. Helps avoid duplicate-content issues if your site is reachable on multiple addresses." />
                    <x-ui.input wire:model="state.seo_og_image" label="Default share image (URL)"
                                  hint="Shown when someone shares any page of your site on social media." />
                </div>
                <div class="mt-4">
                    <x-ui.textarea wire:model="state.seo_meta_description" label="Default page description" rows="3"
                                    hint="Shown in search results and social media when a page has no description of its own." />
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="analytics">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Analytics IDs</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input wire:model="state.analytics_ga4" label="Google Analytics 4 ID" placeholder="G-XXXXXXX" />
                    <x-ui.input wire:model="state.analytics_gtm" label="Google Tag Manager ID" placeholder="GTM-XXXXXXX" />
                    <x-ui.input wire:model="state.analytics_meta_pixel" label="Meta Pixel ID" />
                    <x-ui.input wire:model="state.analytics_hotjar" label="Hotjar ID" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Custom snippets</h2>
                <p class="text-xs text-zinc-500 mb-3">Injected only for users with the <code>developer</code> role. Sanitized otherwise.</p>
                <div class="space-y-4">
                    <x-ui.textarea wire:model="state.analytics_custom_head" label="Inside &lt;head&gt;" rows="4" />
                    <x-ui.textarea wire:model="state.analytics_custom_body" label="End of &lt;body&gt;" rows="4" />
                </div>
            </x-ui.card>
        </x-ui.tab-panel>

        <x-ui.tab-panel name="cookie">
            <x-ui.card>
                <h2 class="text-base font-semibold mb-4">Cookie banner</h2>
                <label class="flex items-center gap-2 mb-4">
                    <input type="checkbox" wire:model.live="state.cookie_enabled" class="size-4 rounded border-zinc-300 text-hk-primary-600">
                    <span class="text-sm">Show cookie banner to first-time visitors</span>
                </label>

                <div class="space-y-4">
                    <x-ui.textarea wire:model="state.cookie_message" label="Banner message" rows="3" />
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-ui.input wire:model="state.cookie_accept_label" label="Accept button label" />
                        <x-ui.input wire:model="state.cookie_reject_label" label="Reject button label" />
                        <x-ui.input wire:model="state.cookie_settings_label" label="Preferences label" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input wire:model="state.cookie_policy_url" label="Privacy / cookie policy page"
                                      placeholder="/privacy" hint="Visitors can click through to read your full policy." />
                        <x-ui.select wire:model="state.cookie_position" label="Banner position"
                                      :options="\App\Core\Support\Choices::cookiePositions()" />
                    </div>
                </div>
            </x-ui.card>
        </x-ui.tab-panel>
    </x-ui.tabs>

    <div class="flex justify-end">
        <x-ui.button wire:click="save">Save changes</x-ui.button>
    </div>
</div>
