<div class="container mx-auto px-4 py-10">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('hotels.index') }}" class="hover:underline">Hotels</a> /
        <span>{{ $hotel->name }}</span>
    </nav>

    @if ($hotel->cover_image)
        <img src="{{ $hotel->cover_image }}" alt="" class="w-full aspect-[21/9] rounded-xl object-cover mb-8">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-semibold">{{ $hotel->name }}</h1>
                    <span class="text-amber-500">{{ str_repeat('★', $hotel->star_rating) }}</span>
                </div>
                @if ($hotel->address)<p class="text-zinc-500 mt-1">{{ $hotel->address }}</p>@endif
            </div>

            @if ($hotel->description)
                <div class="prose dark:prose-invert max-w-none">{!! $hotel->description !!}</div>
            @endif

            @if (! empty($hotel->amenities))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Amenities</h2>
                    <ul class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                        @foreach ($hotel->amenities as $a)
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-hk-primary-500 rounded-full"></span> {{ $a }}</li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            @if ($hotel->rooms->isNotEmpty())
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Rooms</h2>
                    <div class="space-y-3">
                        @foreach ($hotel->rooms as $r)
                            <div class="flex items-start justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 last:border-0 pb-3 last:pb-0">
                                <div>
                                    <p class="font-medium">{{ $r->name }}</p>
                                    <p class="text-xs text-zinc-500">Sleeps {{ $r->capacity_adults }} adults · {{ $r->capacity_children }} children</p>
                                </div>
                                <p class="text-sm font-semibold">{{ $hotel->currency }} {{ number_format((float) $r->price_per_night, 0) }} / night</p>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">From</p>
                <p class="text-3xl font-semibold">{{ $hotel->currency }} {{ number_format((float) $hotel->price_from, 0) }}</p>
                <p class="text-xs text-zinc-500">per night</p>
                <p class="text-xs text-zinc-500 mt-3">Check-in {{ $hotel->check_in }} · Check-out {{ $hotel->check_out }}</p>
            </x-ui.card>

            <x-ui.card>
                <h2 class="font-semibold mb-3">Enquire about this hotel</h2>
                <livewire:hk.enquiry-form
                    source="hotel"
                    :leadable-type="\App\Modules\Hotels\Models\Hotel::class"
                    :leadable-id="$hotel->id"
                    :extras="['check_in' => '', 'check_out' => '', 'guests' => 2, 'rooms' => 1]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
