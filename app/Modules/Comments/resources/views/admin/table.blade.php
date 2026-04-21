<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('comments::comments.title') }}</h1>
        <p class="text-sm text-zinc-500 mt-1">Moderate threaded comments across blog posts and pages.</p>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search body, author…" class="w-72" />
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                <option value="pending">{{ __('comments::comments.moderation.pending') }}</option>
                <option value="approved">{{ __('comments::comments.moderation.approved') }}</option>
                <option value="rejected">{{ __('comments::comments.moderation.rejected') }}</option>
                <option value="spam">{{ __('comments::comments.moderation.spam') }}</option>
            </select>
            <select wire:model.live="type" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All types</option>
                @foreach ($types as $t)
                    <option value="{{ $t }}">{{ class_basename($t) }}</option>
                @endforeach
            </select>
        </div>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left">Author</th>
                    <th class="px-4 py-3 text-left">On</th>
                    <th class="px-4 py-3 text-left">Comment</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($comments as $comment)
                <tr wire:key="cmt-{{ $comment->id }}">
                    <td class="px-4 py-3 align-top">
                        <div class="font-medium">{{ $comment->authorName() }}</div>
                        <div class="text-xs text-zinc-500">{{ $comment->author_email }}</div>
                        @if ($comment->parent_id)
                            <div class="text-[11px] text-zinc-400 mt-0.5">↳ reply</div>
                        @endif
                        <div class="text-[11px] text-zinc-400">{{ $comment->created_at?->diffForHumans() }}</div>
                    </td>
                    <td class="px-4 py-3 align-top text-sm text-zinc-500">
                        {{ class_basename($comment->commentable_type) }}
                        @if ($comment->commentable && property_exists($comment->commentable, 'title'))
                            <div class="text-xs text-zinc-400 mt-0.5">{{ \Illuminate\Support\Str::limit($comment->commentable->title ?? '', 40) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 align-top text-sm max-w-xl">
                        <p class="text-zinc-700 dark:text-zinc-300 line-clamp-4 whitespace-pre-line">{{ $comment->body }}</p>
                    </td>
                    <td class="px-4 py-3 align-top">
                        @switch($comment->status)
                            @case('approved')
                                <x-ui.badge variant="success" size="sm">{{ __('comments::comments.moderation.approved') }}</x-ui.badge>
                                @break
                            @case('rejected')
                                <x-ui.badge variant="danger" size="sm">{{ __('comments::comments.moderation.rejected') }}</x-ui.badge>
                                @break
                            @case('spam')
                                <x-ui.badge variant="danger" size="sm">{{ __('comments::comments.moderation.spam') }}</x-ui.badge>
                                @break
                            @default
                                <x-ui.badge variant="warning" size="sm">{{ __('comments::comments.moderation.pending') }}</x-ui.badge>
                        @endswitch
                    </td>
                    <td class="px-4 py-3 align-top text-right whitespace-nowrap space-x-2">
                        @if ($comment->status !== 'approved')
                            <button wire:click="approve('{{ $comment->id }}')" class="text-xs text-hk-success hover:underline">Approve</button>
                        @endif
                        @if ($comment->status !== 'rejected')
                            <button wire:click="reject('{{ $comment->id }}')" class="text-xs hover:underline">Reject</button>
                        @endif
                        @if ($comment->status !== 'spam')
                            <button wire:click="markSpam('{{ $comment->id }}')" class="text-xs text-zinc-500 hover:underline">Spam</button>
                        @endif
                        <button wire:click="delete('{{ $comment->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-zinc-500">No comments match.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $comments->links() }}</div>
</div>
