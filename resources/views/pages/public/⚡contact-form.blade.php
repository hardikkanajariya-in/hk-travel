<?php

use App\Core\Email\HkMail;
use App\Core\Notifications\HkNotifier;
use App\Models\ContactForm;
use App\Models\ContactSubmission;
use App\Models\User;
use App\Rules\Captcha;
use Livewire\Component;

new class extends Component {
    public string $slug = '';

    public ?ContactForm $form = null;

    /** @var array<string, mixed> */
    public array $values = [];

    public string $captchaToken = '';

    public string $hp_field = ''; // honeypot — must stay empty

    public ?int $hp_open_at = null;

    public ?string $success = null;

    public ?string $error = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->form = ContactForm::bySlug($slug);
        $this->hp_open_at = now()->getTimestamp();

        if ($this->form) {
            foreach ($this->form->fields as $field) {
                $key = $field['key'];
                $this->values[$key] = $field['type'] === 'checkbox' ? false : '';
            }
        }
    }

    public function submit(): void
    {
        if (! $this->form) {
            $this->error = __('Form not found.');

            return;
        }

        // Honeypot: bot filled the trap field, or submitted suspiciously fast.
        if ($this->hp_field !== '' || ($this->hp_open_at && (now()->getTimestamp() - $this->hp_open_at) < 2)) {
            $this->success = (string) ($this->form->setting('success_message') ?: __('Thanks — we\'ll be in touch.'));
            $this->reset(['values']);

            return;
        }

        $rules = $this->buildRules();
        if ($this->form->setting('captcha', true)) {
            $rules['captchaToken'] = ['required', new Captcha];
        }

        $this->validate($rules);

        $store = (bool) $this->form->setting('store_submissions', true);
        $submission = null;

        if ($store) {
            $submission = ContactSubmission::create([
                'form_id' => $this->form->id,
                'data' => $this->values,
                'name' => $this->extract(['name', 'full_name', 'first_name']),
                'email' => $this->extract(['email']),
                'phone' => $this->extract(['phone', 'mobile']),
                'subject' => $this->extract(['subject', 'topic']),
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
                'locale' => app()->getLocale(),
                'status' => 'new',
            ]);
        }

        $this->dispatchNotifications($submission);
        $this->maybeCreateLead($submission);

        $this->success = (string) ($this->form->setting('success_message') ?: __('Thanks — we\'ll be in touch.'));
        $this->reset(['values', 'captchaToken']);

        if ($redirect = $this->form->setting('redirect_url')) {
            $this->redirect($redirect, navigate: true);
        }
    }

    protected function buildRules(): array
    {
        $rules = [];
        foreach ($this->form->fields as $field) {
            $rule = [];
            $rule[] = ($field['required'] ?? false) ? 'required' : 'nullable';
            $rule[] = match ($field['type']) {
                'email' => 'email:rfc',
                'url' => 'url',
                'tel' => 'string',
                'number' => 'numeric',
                'date' => 'date',
                'checkbox' => 'boolean',
                default => 'string',
            };
            if (in_array($field['type'], ['text', 'textarea', 'tel'], true)) {
                $rule[] = 'max:'.((int) ($field['max'] ?? 1000));
            }
            $rules['values.'.$field['key']] = $rule;
        }

        return $rules;
    }

    protected function extract(array $candidates): ?string
    {
        foreach ($candidates as $k) {
            if (! empty($this->values[$k])) {
                return (string) $this->values[$k];
            }
        }

        return null;
    }

    protected function dispatchNotifications(?ContactSubmission $submission): void
    {
        $emails = (array) ($this->form->notify_emails ?: []);
        if (empty($emails)) {
            return;
        }

        $vars = [
            'form_name' => $this->form->name,
            'submission_id' => $submission?->id,
            'data' => $this->values,
            'submitted_at' => now()->toDateTimeString(),
        ];

        $mail = app(HkMail::class);
        foreach ($emails as $to) {
            try {
                $mail->sendTo($to, 'contact.submission', $vars);
            } catch (\Throwable) {
                // template may be missing in fresh installs; skip silently.
            }
        }

        // Also fan out to any users who opted in via the per-event matrix.
        try {
            $notifier = app(HkNotifier::class);
            User::query()->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super-admin']))
                ->get()
                ->each(fn (User $u) => $notifier->notify($u, 'contact.submission', $vars, 'contact.submission'));
        } catch (\Throwable) {
        }
    }

    protected function maybeCreateLead(?ContactSubmission $submission): void
    {
        if (! $this->form->create_lead || ! class_exists(\App\Models\Lead::class)) {
            return;
        }

        try {
            \App\Models\Lead::create([
                'name' => $submission?->name ?: ($this->extract(['name']) ?? 'Anonymous'),
                'email' => $submission?->email,
                'phone' => $submission?->phone,
                'subject' => $submission?->subject ?: $this->form->name,
                'source' => 'contact-form:'.$this->form->slug,
                'pipeline_id' => \App\Models\Pipeline::query()->where('is_default', true)->value('id'),
                'stage' => 'new',
                'data' => $this->values,
            ]);
        } catch (\Throwable) {
        }
    }
};

?>

<div>
    @if (! $form)
        <div class="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200">
            {{ __('Contact form ":slug" was not found.', ['slug' => $slug]) }}
        </div>
    @elseif ($success)
        <div role="status" class="rounded-md border border-green-300 bg-green-50 p-6 text-center text-sm text-green-800 dark:border-green-700 dark:bg-green-950 dark:text-green-200">
            {{ $success }}
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            @if ($form->description)
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $form->description }}</p>
            @endif

            @if ($error)
                <div class="rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-200">{{ $error }}</div>
            @endif

            @foreach ($form->fields as $field)
                @php $key = $field['key']; $modelKey = 'values.'.$key; @endphp
                @switch($field['type'])
                    @case('textarea')
                        <x-ui.textarea
                            wire:model="{{ $modelKey }}"
                            :label="$field['label']"
                            :placeholder="$field['placeholder'] ?? ''"
                            :required="$field['required'] ?? false"
                            :rows="$field['rows'] ?? 4"
                            :error="$errors->first($modelKey)" />
                        @break
                    @case('select')
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $field['label'] }}
                                @if ($field['required'] ?? false)<span class="text-red-500">*</span>@endif
                            </label>
                            <select wire:model="{{ $modelKey }}"
                                    class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <option value="">— {{ __('Select') }} —</option>
                                @foreach (($field['options'] ?? []) as $opt)
                                    <option value="{{ is_array($opt) ? $opt['value'] : $opt }}">{{ is_array($opt) ? $opt['label'] : $opt }}</option>
                                @endforeach
                            </select>
                            @error($modelKey)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        @break
                    @case('checkbox')
                        <label class="flex items-start gap-2 text-sm">
                            <input type="checkbox" wire:model="{{ $modelKey }}" class="mt-0.5 size-4 rounded border-zinc-300 text-hk-primary-600">
                            <span>{{ $field['label'] }}@if ($field['required'] ?? false)<span class="text-red-500">*</span>@endif</span>
                        </label>
                        @error($modelKey)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        @break
                    @default
                        <x-ui.input
                            :type="$field['type'] ?? 'text'"
                            wire:model="{{ $modelKey }}"
                            :label="$field['label']"
                            :placeholder="$field['placeholder'] ?? ''"
                            :required="$field['required'] ?? false"
                            :error="$errors->first($modelKey)" />
                @endswitch
            @endforeach

            {{-- Honeypot trap (hidden from users, visible to bots) --}}
            <div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden">
                <label>Website (leave blank)<input type="text" tabindex="-1" autocomplete="off" wire:model="hp_field"></label>
            </div>

            @if ($form->setting('captcha', true))
                <x-ui.captcha wire:model="captchaToken" action="contact" />
                @error('captchaToken')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            @endif

            <div class="flex items-center justify-between gap-3 pt-2">
                <p class="text-xs text-zinc-500">{{ __('We\'ll never share your details.') }}</p>
                <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">{{ $form->setting('submit_label', __('Send message')) }}</span>
                    <span wire:loading wire:target="submit">{{ __('Sending…') }}</span>
                </x-ui.button>
            </div>
        </form>
    @endif
</div>
