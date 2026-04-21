<?php

use App\Core\Email\EmailTemplateRegistry;
use App\Core\Email\HkMail;
use App\Core\Email\TemplateRenderer;
use App\Core\Localization\LocaleManager;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateTranslation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Email templates')] #[Layout('components.layouts.admin')] class extends Component {
    public ?int $editingId = null;

    public string $key = '';

    public string $label = '';

    public string $description = '';

    public bool $is_active = true;

    public string $locale = 'en';

    public string $subject = '';

    public string $body_html = '';

    public string $body_text = '';

    public string $testEmail = '';

    public ?string $preview = null;

    public function mount(LocaleManager $manager): void
    {
        $this->locale = $manager->default();
    }

    public function with(EmailTemplateRegistry $registry, LocaleManager $manager): array
    {
        return [
            'templates' => EmailTemplate::orderBy('key')->get(),
            'registry' => $registry->all(),
            'languages' => $manager->languages(),
        ];
    }

    public function pickRegistered(string $key, EmailTemplateRegistry $registry): void
    {
        $reg = $registry->get($key);
        if (! $reg) {
            return;
        }

        $template = EmailTemplate::firstOrCreate(
            ['key' => $key],
            [
                'label' => $reg['label'],
                'description' => $reg['description'] ?? null,
                'variables' => $reg['variables'] ?? [],
            ],
        );

        $this->loadTemplate($template->id);
    }

    public function loadTemplate(int $id): void
    {
        $template = EmailTemplate::with('translations')->findOrFail($id);
        $this->editingId = $template->id;
        $this->key = $template->key;
        $this->label = $template->label;
        $this->description = (string) $template->description;
        $this->is_active = $template->is_active;

        $this->loadTranslation();
    }

    public function updatedLocale(): void
    {
        if ($this->editingId) {
            $this->loadTranslation();
        }
    }

    protected function loadTranslation(): void
    {
        $tr = EmailTemplateTranslation::where('email_template_id', $this->editingId)->where('locale', $this->locale)->first();
        $this->subject = $tr->subject ?? '';
        $this->body_html = $tr->body_html ?? '';
        $this->body_text = $tr->body_text ?? '';
    }

    public function saveTranslation(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string|max:200000',
            'body_text' => 'nullable|string|max:200000',
        ]);

        EmailTemplate::where('id', $this->editingId)->update([
            'label' => $this->label,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        EmailTemplateTranslation::updateOrCreate(
            ['email_template_id' => $this->editingId, 'locale' => $this->locale],
            ['subject' => $this->subject, 'body_html' => $this->body_html, 'body_text' => $this->body_text],
        );

        session()->flash('settings.saved', __('Email template saved.'));
    }

    public function previewTemplate(TemplateRenderer $renderer): void
    {
        $sample = $this->sampleVars();
        $this->preview = $renderer->render($this->body_html, $sample);
    }

    public function sendTest(HkMail $mail): void
    {
        $this->validate(['testEmail' => 'required|email']);

        // Persist current edits first so the actual rendered template matches.
        $this->saveTranslation();

        $mail->sendTo($this->testEmail, $this->key, $this->sampleVars(), $this->locale);
        session()->flash('settings.saved', __('Test email queued/sent.'));
    }

    /** @return array<string, mixed> */
    protected function sampleVars(): array
    {
        return [
            'user' => ['name' => 'Sample User', 'email' => 'sample@example.com'],
            'url' => url('/sample/link'),
            'name' => 'Sample User',
            'email' => 'sample@example.com',
            'message' => 'This is a sample preview message.',
            'booking' => ['code' => 'BK-12345', 'total' => '$199.00'],
        ];
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Email templates" subtitle="Customize transactional emails per locale. Use double curly braces around a variable name to insert it." />

    <x-admin.flash :message="session('settings.saved')" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <x-ui.card class="lg:col-span-4">
            <h2 class="text-base font-semibold mb-3">Templates</h2>
            <div class="space-y-1">
                @foreach ($templates as $t)
                    <button type="button" wire:click="loadTemplate({{ $t->id }})"
                            class="w-full text-left px-3 py-2 rounded-md text-sm {{ $editingId === $t->id ? 'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        <div class="font-medium">{{ $t->label }}</div>
                        <div class="text-xs text-zinc-500 font-mono">{{ $t->key }}</div>
                    </button>
                @endforeach
            </div>

            @php $missing = collect($registry)->reject(fn ($v, $k) => $templates->where('key', $k)->isNotEmpty()); @endphp
            @if ($missing->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-semibold mb-2">Available to add</h3>
                    <div class="space-y-1">
                        @foreach ($missing as $k => $v)
                            <button type="button" wire:click="pickRegistered('{{ $k }}')"
                                    class="w-full text-left px-3 py-1.5 rounded-md text-xs hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                <span class="font-mono text-zinc-500">{{ $k }}</span> — {{ $v['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-ui.card>

        <div class="lg:col-span-8 space-y-6">
            @if ($editingId)
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-semibold">{{ $label }}</h2>
                            <p class="text-xs text-zinc-500 font-mono">{{ $key }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm">Locale</label>
                            <select wire:model.live="locale" class="rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm">
                                @foreach ($languages as $lang)
                                    <option value="{{ $lang['code'] }}">{{ $lang['native_name'] ?: $lang['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <x-ui.input wire:model="subject" label="Subject" required />

                        <div>
                            <label class="block text-sm font-medium mb-1">Body (HTML)</label>
                            <x-ui.rich-text wire:model="body_html" rows="14" />
                            @php
                                $vars = EmailTemplate::find($editingId)?->variables ?? [];
                                $varHints = collect($vars)->map(fn ($v) => '{{ '.$v.' }}')->implode(' ');
                            @endphp
                            <p class="text-xs text-zinc-500 mt-1">
                                Variables: <span class="font-mono">{{ $varHints }}</span>
                            </p>
                        </div>

                        <x-ui.textarea wire:model="body_text" label="Plain-text fallback (optional)" rows="6" />

                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_active" class="size-4 rounded">
                            <span class="text-sm">Active</span>
                        </label>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 justify-end">
                        <x-ui.button variant="ghost" wire:click="previewTemplate">Preview</x-ui.button>
                        <x-ui.button wire:click="saveTranslation">Save</x-ui.button>
                    </div>
                </x-ui.card>

                @if ($preview)
                    <x-ui.card>
                        <h3 class="text-sm font-semibold mb-2">Preview</h3>
                        <div class="rounded-md border border-zinc-200 dark:border-zinc-800 p-4 bg-white dark:bg-zinc-950">
                            {!! $preview !!}
                        </div>
                    </x-ui.card>
                @endif

                <x-ui.card>
                    <h3 class="text-sm font-semibold mb-3">Send test</h3>
                    <div class="flex gap-2">
                        <x-ui.input type="email" wire:model="testEmail" placeholder="you@example.com" />
                        <x-ui.button wire:click="sendTest">Send</x-ui.button>
                    </div>
                </x-ui.card>
            @else
                <x-ui.card>
                    <p class="text-sm text-zinc-500">Select a template from the left to start editing.</p>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>
