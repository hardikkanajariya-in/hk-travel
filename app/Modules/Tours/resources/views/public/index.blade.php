<div class="mx-auto max-w-6xl px-6 py-12">
    <div class="mb-8 flex items-end justify-between">
        <h1 class="text-3xl font-bold">{{ __('tours::tours.title') }}</h1>
        <x-ui.input wire:model.live.debounce.500ms="search" placeholder="Search tours…" class="w-72" />
    </div>

    @if ($tours->isEmpty())
        <x-ui.card>
            <p class="text-center text-zinc-500">No tours published yet.</p>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($tours as $tour)
                <x-ui.card>
                    <h2 class="font-semibold">{{ $tour->name }}</h2>
                    <p class="text-sm text-zinc-500 mt-1">{{ $tour->duration_days }} days</p>
                    <p class="mt-3 text-lg font-bold">{{ number_format($tour->price, 2) }}</p>
                    <a href="{{ route('tours.show', $tour->slug) }}" wire:navigate
                       class="mt-3 inline-block text-sm text-hk-primary-600 hover:underline">View details →</a>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-8">{{ $tours->links() }}</div>
    @endif
</div>
