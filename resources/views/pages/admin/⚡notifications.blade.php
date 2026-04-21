<?php

use App\Core\Notifications\NotificationRegistry;
use App\Models\NotificationChannel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Notifications')] #[Layout('components.layouts.admin')] class extends Component {
    /** @var array<int, array{id:int, key:string, label:string, description:?string, is_enabled:bool}> */
    public array $channels = [];

    public function mount(): void
    {
        $this->loadChannels();
    }

    public function with(NotificationRegistry $registry): array
    {
        return ['events' => $registry->all()];
    }

    public function toggleChannel(int $id): void
    {
        $channel = NotificationChannel::findOrFail($id);
        $channel->update(['is_enabled' => ! $channel->is_enabled]);
        $this->loadChannels();
        session()->flash('settings.saved', __('Channel updated.'));
    }

    protected function loadChannels(): void
    {
        $this->channels = NotificationChannel::orderBy('id')->get()->map(fn (NotificationChannel $c): array => [
            'id' => $c->id,
            'key' => $c->key,
            'label' => $c->label,
            'description' => $c->description,
            'is_enabled' => $c->is_enabled,
        ])->all();
    }
};

?>

<div class="space-y-6">
    <x-admin.page-header title="Notifications" subtitle="Manage delivery channels and the global event matrix." />

    <x-admin.flash :message="session('settings.saved')" />

    <x-ui.card>
        <h2 class="text-base font-semibold mb-4">Channels</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($channels as $c)
                <div class="flex items-start justify-between gap-3 p-3 rounded-md border border-zinc-200 dark:border-zinc-800">
                    <div>
                        <div class="font-medium">{{ $c['label'] }} <span class="text-xs text-zinc-500 font-mono">({{ $c['key'] }})</span></div>
                        @if ($c['description'])
                            <div class="text-xs text-zinc-500 mt-0.5">{{ $c['description'] }}</div>
                        @endif
                    </div>
                    <button type="button" wire:click="toggleChannel({{ $c['id'] }})"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors {{ $c['is_enabled'] ? 'bg-hk-primary-600' : 'bg-zinc-300 dark:bg-zinc-700' }}">
                        <span class="inline-block size-5 transform rounded-full bg-white shadow transition-transform {{ $c['is_enabled'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-base font-semibold mb-4">Events</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="text-left py-2 pr-4">Event</th>
                        <th class="text-left py-2 pr-4">Audience</th>
                        <th class="text-left py-2">Allowed channels</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $key => $event)
                        <tr class="border-b border-zinc-100 dark:border-zinc-900">
                            <td class="py-2 pr-4">
                                <div class="font-medium">{{ $event['label'] }}</div>
                                <div class="text-xs text-zinc-500 font-mono">{{ $key }}</div>
                                @if ($event['description'] ?? null)
                                    <div class="text-xs text-zinc-500 mt-1">{{ $event['description'] }}</div>
                                @endif
                            </td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-zinc-100 dark:bg-zinc-800">{{ $event['audience'] }}</span>
                            </td>
                            <td class="py-2">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($event['channels'] as $ch)
                                        <span class="px-2 py-0.5 text-xs rounded-md border border-zinc-200 dark:border-zinc-800">{{ $ch }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-zinc-500 mt-3">Per-user opt-in/out is managed on each user's profile preferences page.</p>
    </x-ui.card>
</div>
