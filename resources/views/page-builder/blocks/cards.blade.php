@php
    $cols = (int) ($data['columns'] ?? 3);
    $grid = match ($cols) {
        2 => 'sm:grid-cols-2',
        4 => 'sm:grid-cols-2 lg:grid-cols-4',
        default => 'sm:grid-cols-2 lg:grid-cols-3',
    };
    $cards = $data['cards'] ?? [];
@endphp

@if (! empty($cards))
    <div class="grid grid-cols-1 {{ $grid }} gap-6">
        @foreach ($cards as $card)
            <article class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                @if (! empty($card['image']))
                    <img src="{{ $card['image'] }}" alt=""
                         loading="lazy" decoding="async"
                         class="aspect-[16/10] w-full object-cover transition group-hover:scale-[1.02]">
                @endif
                <div class="flex flex-1 flex-col p-5">
                    @if (! empty($card['title']))
                        <h3 class="text-lg font-semibold">{{ $card['title'] }}</h3>
                    @endif
                    @if (! empty($card['body']))
                        <p class="mt-1 flex-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $card['body'] }}</p>
                    @endif
                    @if (! empty($card['cta_label']))
                        <a href="{{ $card['cta_url'] ?? '#' }}"
                           class="mt-4 inline-flex items-center text-sm font-semibold text-hk-primary-600 hover:underline">
                            {{ $card['cta_label'] }} →
                        </a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
