<div class="container mx-auto px-4 py-10 max-w-6xl">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('cruises.index') }}" class="hover:underline">Cruises</a> /
        <span>{{ $cruise->title }}</span>
    </nav>

    @if ($cruise->cover_image)
        <div class="rounded-2xl overflow-hidden mb-6">
            <img src="{{ $cruise->cover_image }}" alt="{{ $cruise->title }}" class="w-full aspect-[16/7] object-cover">
        </div>
    @endif

    <header class="mb-6">
        <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ $cruise->cruise_line }} @if ($cruise->ship_name) · {{ $cruise->ship_name }}@endif</p>
        <h1 class="text-3xl md:text-4xl font-semibold mt-1">{{ $cruise->title }}</h1>
        <p class="text-zinc-500 mt-2">{{ $cruise->departure_port }} → {{ $cruise->arrival_port }} · {{ $cruise->duration_nights }} nights @if ($cruise->departure_date) · departs {{ $cruise->departure_date->format('d M Y') }}@endif</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            @if ($cruise->description)
                <div class="prose dark:prose-invert max-w-none">{!! $cruise->description !!}</div>
            @endif

            @if (! empty($cruise->itinerary))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Itinerary</h2>
                    <ol class="space-y-3">
                        @foreach ($cruise->itinerary as $day)
                            <li class="flex gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-3 last:border-b-0">
                                <span class="font-semibold w-16 shrink-0 text-hk-primary-600">Day {{ $day['day'] ?? '' }}</span>
                                <div>
                                    <p class="font-medium">{{ $day['port'] ?? '' }}</p>
                                    <p class="text-sm text-zinc-500 mt-1">{{ $day['activity'] ?? '' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </x-ui.card>
            @endif

            @if (! empty($cruise->cabin_types))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Cabin types</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($cruise->cabin_types as $cabin)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <p class="font-medium">{{ $cabin['name'] ?? '' }}</p>
                                <p class="text-xs text-zinc-500">Sleeps {{ $cabin['capacity'] ?? '' }}</p>
                                <p class="text-lg font-semibold mt-1">{{ $cruise->currency }} {{ number_format((float) ($cabin['price'] ?? 0), 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if (! empty($cruise->inclusions))
                    <x-ui.card>
                        <h2 class="font-semibold mb-3">What's included</h2>
                        <ul class="space-y-1 text-sm">
                            @foreach ($cruise->inclusions as $i)<li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $i }}</li>@endforeach
                        </ul>
                    </x-ui.card>
                @endif
                @if (! empty($cruise->exclusions))
                    <x-ui.card>
                        <h2 class="font-semibold mb-3">Not included</h2>
                        <ul class="space-y-1 text-sm">
                            @foreach ($cruise->exclusions as $e)<li class="flex gap-2"><span class="text-rose-500">✗</span> {{ $e }}</li>@endforeach
                        </ul>
                    </x-ui.card>
                @endif
            </div>
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">From</p>
                <p class="text-3xl font-semibold">{{ $cruise->currency }} {{ number_format((float) $cruise->price_from, 0) }}</p>
                <p class="text-xs text-zinc-500">per person</p>
            </x-ui.card>
            <x-ui.card>
                <h2 class="font-semibold mb-3">Request a quote</h2>
                <livewire:hk.enquiry-form
                    source="cruise"
                    :leadable-type="\App\Modules\Cruises\Models\Cruise::class"
                    :leadable-id="$cruise->id"
                    :extras="['preferred_cabin' => '', 'travellers' => 2]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
