<div>
    @php $schema = $destination->toSeoMeta()['schema'] ?? null; @endphp
    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <section class="relative">
        <div class="aspect-[16/6] bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
            @if ($destination->cover_image)
                <img src="{{ $destination->cover_image }}" alt="{{ $destination->name }}" class="h-full w-full object-cover">
            @endif
        </div>
        <div class="mx-auto max-w-7xl px-6 -mt-16 relative">
            <div class="rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 md:p-8 shadow-sm">
                <nav class="text-xs text-zinc-500 mb-2">
                    <a href="{{ route('destinations.index') }}" wire:navigate class="hover:underline">Destinations</a>
                    <span class="mx-1">/</span>
                    <span>{{ $destination->name }}</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-bold">{{ $destination->name }}</h1>
                <p class="mt-1 text-sm text-zinc-500 capitalize">{{ $destination->type }} @if ($destination->country_code) · {{ $destination->country_code }} @endif</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-8">
            @if ($destination->description)
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($destination->description)) !!}
                </div>
            @endif

            @if ($destination->highlights)
                <x-ui.card>
                    <h2 class="text-lg font-semibold mb-3">Highlights</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $destination->highlights }}</p>
                </x-ui.card>
            @endif

            @foreach (['tours' => 'Tours', 'hotels' => 'Hotels', 'activities' => 'Activities'] as $key => $label)
                @if ($related[$key]->isNotEmpty())
                    <div>
                        <h2 class="text-xl font-semibold mb-4">{{ $label }} in {{ $destination->name }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($related[$key] as $item)
                                <x-ui.card>
                                    <h3 class="font-semibold">{{ $item->name }}</h3>
                                    @if (isset($item->price))
                                        <p class="mt-1 text-sm text-zinc-500">From {{ number_format((float) $item->price, 2) }}</p>
                                    @endif
                                </x-ui.card>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <aside class="space-y-4">
            <livewire:hk.enquiry-form
                source="destination"
                heading="Plan a trip here"
                :leadable-type="get_class($destination)"
                :leadable-id="$destination->id"
            />
        </aside>
    </section>
</div>
