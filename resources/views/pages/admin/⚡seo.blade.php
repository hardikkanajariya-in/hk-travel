<?php

use App\Core\Seo\SitemapGenerator;
use App\Core\Settings\SettingsRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('SEO & Sitemaps')] #[Layout('components.layouts.admin')] class extends Component {
    public string $defaultTitle = '';

    public string $defaultTagline = '';

    public string $defaultDescription = '';

    public string $defaultImage = '';

    public bool $noindexSite = false;

    public string $robotsTxt = '';

    public ?string $flash = null;

    public ?string $sitemapResult = null;

    public function mount(SettingsRepository $settings): void
    {
        $this->defaultTitle = (string) $settings->get('seo.default_title', '');
        $this->defaultTagline = (string) $settings->get('seo.default_tagline', '');
        $this->defaultDescription = (string) $settings->get('seo.default_description', '');
        $this->defaultImage = (string) $settings->get('seo.default_image', '');
        $this->noindexSite = (bool) $settings->get('seo.noindex_site', false);
        $this->robotsTxt = (string) $settings->get('seo.robots_txt', '');
    }

    public function save(SettingsRepository $settings): void
    {
        $this->validate([
            'defaultTitle' => ['nullable', 'string', 'max:120'],
            'defaultTagline' => ['nullable', 'string', 'max:200'],
            'defaultDescription' => ['nullable', 'string', 'max:300'],
            'defaultImage' => ['nullable', 'url', 'max:500'],
            'robotsTxt' => ['nullable', 'string', 'max:8000'],
        ]);

        $settings->setMany([
            'seo.default_title' => $this->defaultTitle ?: null,
            'seo.default_tagline' => $this->defaultTagline ?: null,
            'seo.default_description' => $this->defaultDescription ?: null,
            'seo.default_image' => $this->defaultImage ?: null,
            'seo.noindex_site' => $this->noindexSite,
            'seo.robots_txt' => $this->robotsTxt ?: null,
        ]);

        $this->flash = 'Saved.';
    }

    public function regenerate(SitemapGenerator $generator): void
    {
        $result = $generator->generate();
        $this->sitemapResult = 'Generated '.count($result['children']).' child sitemap(s).';
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="SEO & Sitemaps" description="Set default meta defaults, manage robots.txt, regenerate the sitemap.">
        <x-slot:actions>
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-sm text-hk-primary-600 hover:underline">View sitemap.xml</a>
            <a href="{{ url('/robots.txt') }}" target="_blank" class="text-sm text-hk-primary-600 hover:underline">View robots.txt</a>
            <x-ui.button wire:click="save">Save</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($flash)
        <x-admin.flash :message="$flash" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ui.card>
            <h3 class="text-sm font-semibold mb-3">Default meta</h3>
            <div class="space-y-3">
                <x-ui.input wire:model="defaultTitle" label="Default title" hint="Shown when a page hasn't set its own title." />
                <x-ui.input wire:model="defaultTagline" label="Tagline" hint="Appears next to the site name on the home page." />
                <x-ui.textarea wire:model="defaultDescription" label="Default description" rows="3" hint="Recommended length: 140–160 characters." />
                <x-ui.input wire:model="defaultImage" label="Default OG image URL" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-sm font-semibold mb-3">Robots & indexing</h3>
            <div class="space-y-3">
                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" wire:model="noindexSite" class="mt-0.5 size-4 rounded border-zinc-300 text-hk-primary-600">
                    <span>
                        <span class="font-medium">Hide entire site from search engines</span>
                        <span class="block text-xs text-zinc-500">Adds <code>noindex, nofollow</code> on every page and serves a deny-all robots.txt.</span>
                    </span>
                </label>

                <x-ui.textarea wire:model="robotsTxt"
                               label="Custom robots.txt"
                               rows="10"
                               hint="Leave blank to use the auto-generated default (allows everything except /admin, /dashboard, /livewire and links to your sitemap)." />
            </div>
        </x-ui.card>

        <x-ui.card class="lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold">Sitemap</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Regenerates <code>sitemap.xml</code> plus one file per content source under <code>/sitemaps/</code>.</p>
                </div>
                <x-ui.button variant="outline" wire:click="regenerate">Regenerate now</x-ui.button>
            </div>
            @if ($sitemapResult)
                <p class="mt-3 text-sm text-green-600">{{ $sitemapResult }}</p>
            @endif
        </x-ui.card>
    </div>
</div>
