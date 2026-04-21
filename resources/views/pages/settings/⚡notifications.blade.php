<?php

use App\Core\Notifications\NotificationRegistry;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Notification preferences')] #[Layout('components.layouts.app')] class extends Component {
    /** @var array<string, array<string, bool>> */
    public array $preferences = [];

    public function mount(): void
    {
        $this->loadPreferences();
    }

    public function with(NotificationRegistry $registry): array
    {
        return [
            'events' => collect($registry->all())->filter(fn ($e) => $e['audience'] === 'user'),
            'channels' => NotificationChannel::where('is_enabled', true)->get(),
        ];
    }

    protected function loadPreferences(): void
    {
        $rows = NotificationPreference::where('user_id', Auth::id())->get();

        foreach ($rows as $row) {
            $this->preferences[$row->event_key][$row->channel] = (bool) $row->opted_in;
        }
    }

    public function toggle(string $eventKey, string $channel): void
    {
        $current = $this->preferences[$eventKey][$channel] ?? true;
        $next = ! $current;

        NotificationPreference::updateOrCreate(
            ['user_id' => Auth::id(), 'event_key' => $eventKey, 'channel' => $channel],
            ['opted_in' => $next],
        );

        $this->preferences[$eventKey][$channel] = $next;
        session()->flash('settings.saved', __('Preferences updated.'));
    }
};

?>

<div class="space-y-6">
    <h1 class="text-xl font-semibold">Notification preferences</h1>
    <x-admin.flash :message="session('settings.saved')" />

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="text-left py-2 pr-4">Event</th>
                        @foreach ($channels as $ch)
                            <th class="text-center py-2 px-2">{{ $ch->label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $key => $event)
                        <tr class="border-b border-zinc-100 dark:border-zinc-900">
                            <td class="py-2 pr-4">
                                <div class="font-medium">{{ $event['label'] }}</div>
                                @if ($event['description'] ?? null)
                                    <div class="text-xs text-zinc-500">{{ $event['description'] }}</div>
                                @endif
                            </td>
                            @foreach ($channels as $ch)
                                @php $allowed = in_array($ch->key, $event['channels'], true); @endphp
                                <td class="text-center py-2 px-2">
                                    @if ($allowed)
                                        <input type="checkbox"
                                               @checked($preferences[$key][$ch->key] ?? true)
                                               wire:click="toggle('{{ $key }}', '{{ $ch->key }}')"
                                               class="size-4 rounded">
                                    @else
                                        <span class="text-zinc-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
