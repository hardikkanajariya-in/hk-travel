<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ __('reviews::reviews.title') }}</h1>
        <p class="text-sm text-zinc-500 mt-1">Moderate user-submitted reviews across every reviewable model.</p>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.card :padded="false">
        <div class="flex flex-wrap gap-3 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search title, body, author…" class="w-72" />
            <select wire:model.live="status" class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                <option value="pending">{{ __('reviews::reviews.moderation.pending') }}</option>
                <option value="approved">{{ __('reviews::reviews.moderation.approved') }}</option>
                <option value="rejected">{{ __('reviews::reviews.moderation.rejected') }}</option>
                <option value="spam">{{ __('reviews::reviews.moderation.spam') }}</option>
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
                    <th class="px-4 py-3 text-left">Subject</th>
                    <th class="px-4 py-3 text-left">Review</th>
                    <th class="px-4 py-3 text-left">Rating</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($reviews as $review)
                <tr wire:key="rev-{{ $review->id }}">
                    <td class="px-4 py-3 align-top">
                        <div class="font-medium">{{ $review->authorName() }}</div>
                        <div class="text-xs text-zinc-500">{{ $review->author_email }}</div>
                        <div class="text-[11px] text-zinc-400 mt-0.5">{{ $review->created_at?->diffForHumans() }}</div>
                    </td>
                    <td class="px-4 py-3 align-top text-sm">
                        <span class="text-zinc-500">{{ class_basename($review->reviewable_type) }}</span>
                        @if ($review->reviewable && property_exists($review->reviewable, 'name'))
                            <div class="text-xs text-zinc-400 mt-0.5">{{ $review->reviewable->name ?? '' }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 align-top text-sm max-w-md">
                        @if ($review->title)
                            <div class="font-medium">{{ $review->title }}</div>
                        @endif
                        <p class="text-zinc-600 dark:text-zinc-300 line-clamp-3">{{ $review->body }}</p>
                    </td>
                    <td class="px-4 py-3 align-top text-amber-500 whitespace-nowrap">★ {{ number_format((float) $review->rating, 1) }}</td>
                    <td class="px-4 py-3 align-top">
                        @switch($review->status)
                            @case('approved')
                                <x-ui.badge variant="success" size="sm">{{ __('reviews::reviews.moderation.approved') }}</x-ui.badge>
                                @break
                            @case('rejected')
                                <x-ui.badge variant="danger" size="sm">{{ __('reviews::reviews.moderation.rejected') }}</x-ui.badge>
                                @break
                            @case('spam')
                                <x-ui.badge variant="danger" size="sm">{{ __('reviews::reviews.moderation.spam') }}</x-ui.badge>
                                @break
                            @default
                                <x-ui.badge variant="warning" size="sm">{{ __('reviews::reviews.moderation.pending') }}</x-ui.badge>
                        @endswitch
                    </td>
                    <td class="px-4 py-3 align-top text-right whitespace-nowrap space-x-2">
                        @if ($review->status !== 'approved')
                            <button wire:click="approve('{{ $review->id }}')" class="text-xs text-hk-success hover:underline">Approve</button>
                        @endif
                        @if ($review->status !== 'rejected')
                            <button wire:click="reject('{{ $review->id }}')" class="text-xs hover:underline">Reject</button>
                        @endif
                        @if ($review->status !== 'spam')
                            <button wire:click="markSpam('{{ $review->id }}')" class="text-xs text-zinc-500 hover:underline">Spam</button>
                        @endif
                        <button wire:click="delete('{{ $review->id }}')" wire:confirm="{{ __('admin.confirm.delete') }}" class="text-xs text-hk-danger hover:underline">{{ __('admin.actions.delete') }}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">No reviews match.</td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <div>{{ $reviews->links() }}</div>
</div>
