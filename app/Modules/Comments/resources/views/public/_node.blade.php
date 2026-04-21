<li id="comment-{{ $node->id }}" wire:key="c-{{ $node->id }}" class="space-y-3">
    <article class="rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
        <header class="flex items-start justify-between gap-3">
            <div>
                <p class="font-medium text-sm">
                    @if ($node->author_url)
                        <a href="{{ $node->author_url }}" rel="nofollow ugc noopener" target="_blank" class="hover:underline">{{ $node->authorName() }}</a>
                    @else
                        {{ $node->authorName() }}
                    @endif
                    @if ($node->is_pinned)
                        <span class="ml-1 text-[10px] uppercase tracking-wider text-hk-primary-600">Pinned</span>
                    @endif
                </p>
                <p class="text-xs text-zinc-500"><time datetime="{{ $node->approved_at?->toIso8601String() }}">{{ $node->approved_at?->diffForHumans() }}</time></p>
            </div>
            @if ($allowComments)
                <button type="button" wire:click="setReplyTo('{{ $node->id }}')" class="text-xs text-hk-primary-600 hover:underline">{{ __('comments::comments.reply') }}</button>
            @endif
        </header>
        <div class="mt-3 text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $node->body }}</div>
    </article>

    @if (($node->approvedReplies ?? collect())->isNotEmpty())
        <ol class="space-y-3 pl-4 sm:pl-8 border-l border-zinc-200 dark:border-zinc-800">
            @foreach ($node->approvedReplies as $child)
                @include('comments::public._node', ['node' => $child])
            @endforeach
        </ol>
    @endif
</li>
