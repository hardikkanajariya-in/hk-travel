<div>
    @if ($sent)
        <x-ui.alert variant="success">{{ __('reviews::reviews.thanks') }}</x-ui.alert>
    @else
        <form wire:submit="submit" class="space-y-4">
            <x-ui.honeypot wire:model="hp_field" />

            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('reviews::reviews.rating') }}</label>
                <div class="flex items-center gap-2" x-data="{ hover: 0 }">
                    @foreach (range(1, 5) as $i)
                        <button type="button"
                                wire:click="$set('rating', {{ $i }})"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                class="text-2xl transition"
                                :class="(hover >= {{ $i }}) || (!hover && {{ $rating }} >= {{ $i }}) ? 'text-amber-400' : 'text-zinc-300 dark:text-zinc-700'">★</button>
                    @endforeach
                    <span class="text-sm text-zinc-500 ml-2">{{ $rating }}/5</span>
                </div>
                @error('rating') <p class="text-xs text-hk-danger mt-1">{{ $message }}</p> @enderror
            </div>

            @if (! empty($criteriaKeys))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($criteriaKeys as $key)
                        <div>
                            <label class="block text-xs font-medium mb-1">{{ __('reviews::reviews.criteria.'.$key, [], app()->getLocale()) ?? ucfirst($key) }}</label>
                            <x-ui.select
                                wire:model="criteria.{{ $key }}"
                                :options="\App\Core\Support\Choices::reviewScoreOptions()"
                            />
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-ui.input wire:model="authorName" label="Your name" required :error="$errors->first('authorName')" />
                <x-ui.input type="email" wire:model="authorEmail" label="Email (kept private)" required :error="$errors->first('authorEmail')" />
            </div>

            <x-ui.input wire:model="title" label="Title (optional)" :error="$errors->first('title')" />
            <x-ui.textarea wire:model="body" label="Your review" rows="5" required :error="$errors->first('body')" />

            <x-ui.captcha action="review" />
            @error('captchaToken') <p class="text-xs text-hk-danger">{{ $message }}</p> @enderror

            <div class="flex items-center justify-between">
                <p class="text-xs text-zinc-500">Reviews are moderated before publishing.</p>
                <x-ui.button type="submit" variant="primary">
                    <span wire:loading.remove wire:target="submit">{{ __('reviews::reviews.submit') }}</span>
                    <span wire:loading wire:target="submit">{{ __('Sending…') }}</span>
                </x-ui.button>
            </div>
        </form>
    @endif
</div>
