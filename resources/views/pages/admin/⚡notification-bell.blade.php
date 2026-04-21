<?php

use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public int $unread = 0;

    /** @var array<int, array{id:string, type:string, data:array<string, mixed>, created_at:string, read_at:?string}> */
    public array $items = [];

    public bool $open = false;

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $rows = DB::table('notifications')
            ->where('notifiable_type', $user::class)
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $this->items = $rows->map(fn ($n): array => [
            'id' => $n->id,
            'type' => $n->type,
            'data' => json_decode((string) $n->data, true) ?: [],
            'created_at' => (string) $n->created_at,
            'read_at' => $n->read_at,
        ])->all();

        $this->unread = (int) DB::table('notifications')
            ->where('notifiable_type', $user::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function markAllRead(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_type', $user::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->refresh();
    }
};

?>

<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open; if (open) $wire.refresh()" class="relative rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($unread > 0)
            <span class="absolute top-0.5 right-0.5 inline-flex size-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unread > 9 ? '9+' : $unread }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.outside="open = false" x-cloak x-transition
         class="absolute right-0 mt-2 w-80 rounded-md border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg z-50">
        <div class="flex items-center justify-between px-3 py-2 border-b border-zinc-200 dark:border-zinc-800">
            <span class="text-sm font-semibold">Notifications</span>
            @if ($unread > 0)
                <button type="button" wire:click="markAllRead" class="text-xs text-hk-primary-600 hover:underline">Mark all read</button>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse ($items as $item)
                <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-900 text-sm {{ $item['read_at'] ? 'opacity-60' : '' }}">
                    <div class="font-medium">{{ $item['type'] }}</div>
                    @if (! empty($item['data']['message']))
                        <div class="text-xs text-zinc-500 mt-0.5">{{ $item['data']['message'] }}</div>
                    @endif
                    <div class="text-[10px] text-zinc-400 mt-1">{{ $item['created_at'] }}</div>
                </div>
            @empty
                <div class="px-3 py-6 text-center text-sm text-zinc-500">No notifications.</div>
            @endforelse
        </div>
    </div>
</div>
