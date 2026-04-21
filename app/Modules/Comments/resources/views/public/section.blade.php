<div id="comments" class="space-y-8 scroll-mt-24">
    <h2 class="text-2xl font-semibold">
        {{ trans_choice('comments::comments.count', $count, ['count' => $count]) }}
    </h2>

    @if ($tree->isEmpty())
        <p class="text-sm text-zinc-500 italic">{{ __('comments::comments.no_comments') }}</p>
    @else
        <ol class="space-y-6">
            @foreach ($tree as $node)
                @include('comments::public._node', ['node' => $node])
            @endforeach
        </ol>
    @endif

    @if ($allowComments)
        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-6">
            @if ($sent)
                <x-ui.alert variant="success">{{ __('comments::comments.thanks') }}</x-ui.alert>
            @else
                <h3 class="text-lg font-semibold mb-4">
                    @if ($replyToName)
                        {{ __('comments::comments.form.reply_heading', ['name' => $replyToName]) }}
                        <button type="button" wire:click="setReplyTo(null)" class="ml-2 text-xs font-normal text-hk-primary-600 hover:underline">{{ __('comments::comments.form.cancel_reply') }}</button>
                    @else
                        {{ __('comments::comments.form.heading') }}
                    @endif
                </h3>

                <form wire:submit="submit" class="space-y-4">
                    <x-ui.honeypot wire:model="hp_field" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input wire:model="authorName" label="{{ __('comments::comments.form.name') }}" required :error="$errors->first('authorName')" />
                        <x-ui.input type="email" wire:model="authorEmail" label="{{ __('comments::comments.form.email') }}" required :error="$errors->first('authorEmail')" />
                    </div>

                    <x-ui.input wire:model="authorUrl" label="{{ __('comments::comments.form.website') }}" :error="$errors->first('authorUrl')" />
                    <x-ui.textarea wire:model="body" label="{{ __('comments::comments.form.body') }}" rows="5" required :error="$errors->first('body')" />

                    <x-ui.captcha action="comment" />
                    @error('captchaToken') <p class="text-xs text-hk-danger">{{ $message }}</p> @enderror

                    <div class="flex items-center justify-end">
                        <x-ui.button type="submit" variant="primary">
                            <span wire:loading.remove wire:target="submit">{{ __('comments::comments.form.submit') }}</span>
                            <span wire:loading wire:target="submit">{{ __('Sending…') }}</span>
                        </x-ui.button>
                    </div>
                </form>
            @endif
        </div>
    @else
        <p class="text-sm text-zinc-500 italic border-t border-zinc-200 dark:border-zinc-800 pt-6">{{ __('comments::comments.closed') }}</p>
    @endif
</div>
