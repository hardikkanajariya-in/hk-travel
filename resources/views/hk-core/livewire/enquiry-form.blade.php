<div>
    <x-ui.card>
        <h3 class="text-lg font-semibold mb-4">{{ $heading }}</h3>

        @if ($sent)
            <x-ui.alert variant="success">
                Thanks — we've received your enquiry and will get back to you shortly.
            </x-ui.alert>
        @else
            <form wire:submit="submit" class="space-y-4">
                <x-ui.honeypot wire:model="hp_field" />

                <x-ui.input wire:model="name" label="Your name" required :error="$errors->first('name')" />
                <x-ui.input type="email" wire:model="email" label="Email" required :error="$errors->first('email')" />
                <x-ui.input wire:model="phone" label="Phone (optional)" :error="$errors->first('phone')" />

                @foreach ($extraFields as $f)
                    @php $key = $f['key']; $type = $f['type'] ?? 'text'; @endphp
                    @if ($type === 'select' && ! empty($f['options']))
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $f['label'] }}
                                @if ($f['required'] ?? false)<span class="text-hk-danger">*</span>@endif
                            </label>
                            <select wire:model="extra.{{ $key }}"
                                class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                                <option value="">— choose —</option>
                                @foreach ($f['options'] as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('extra.'.$key)<p class="text-xs text-hk-danger">{{ $message }}</p>@enderror
                        </div>
                    @elseif ($type === 'date')
                        <x-ui.input type="date" wire:model="extra.{{ $key }}" :label="$f['label']" :required="$f['required'] ?? false"
                                    :error="$errors->first('extra.'.$key)" />
                    @elseif ($type === 'number')
                        <x-ui.input type="number" wire:model="extra.{{ $key }}" :label="$f['label']" :required="$f['required'] ?? false"
                                    :error="$errors->first('extra.'.$key)" />
                    @else
                        <x-ui.input wire:model="extra.{{ $key }}" :label="$f['label']" :required="$f['required'] ?? false"
                                    :error="$errors->first('extra.'.$key)" />
                    @endif
                @endforeach

                <x-ui.textarea wire:model="message" label="Message (optional)" rows="4"
                               :error="$errors->first('message')" />

                <x-ui.captcha wire:model="captchaToken" form="enquiry" :error="$errors->first('captchaToken')" />

                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Send enquiry</span>
                    <span wire:loading wire:target="submit">Sending…</span>
                </x-ui.button>
            </form>
        @endif
    </x-ui.card>
</div>
