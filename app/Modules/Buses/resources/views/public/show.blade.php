<div class="container mx-auto px-4 py-10 max-w-5xl">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('buses.index') }}" class="hover:underline">Buses</a> /
        <span>{{ $route->title }}</span>
    </nav>

    <header class="mb-6">
        <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ $route->operator }} · {{ ucfirst($route->bus_type) }}</p>
        <h1 class="text-3xl font-semibold mt-1">{{ $route->origin }} → {{ $route->destination }}</h1>
        <p class="text-zinc-500 mt-1">{{ $route->departure_time }} – {{ $route->arrival_time }} · {{ floor($route->duration_minutes / 60) }}h {{ $route->duration_minutes % 60 }}m · {{ $route->distance_km }} km</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            @if ($route->description)
                <div class="prose dark:prose-invert max-w-none">{!! $route->description !!}</div>
            @endif

            @if (! empty($route->stops))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Stops</h2>
                    <ol class="space-y-1 text-sm list-decimal pl-5">
                        @foreach ($route->stops as $s)<li>{{ $s }}</li>@endforeach
                    </ol>
                </x-ui.card>
            @endif

            @if (! empty($route->amenities))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Amenities</h2>
                    <ul class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ($route->amenities as $a)<li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $a }}</li>@endforeach
                    </ul>
                </x-ui.card>
            @endif

            @if (! empty($route->schedule_days))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Operating days</h2>
                    <div class="flex flex-wrap gap-2 text-sm">
                        @foreach ($route->schedule_days as $d)<x-ui.badge variant="neutral">{{ ucfirst($d) }}</x-ui.badge>@endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">Fare</p>
                <p class="text-3xl font-semibold">{{ $route->currency }} {{ number_format((float) $route->fare, 0) }}</p>
                <p class="text-xs text-zinc-500">per seat</p>
            </x-ui.card>

            <x-ui.card>
                <h2 class="font-semibold mb-3">Reserve seats</h2>
                <livewire:hk.enquiry-form
                    source="bus"
                    :leadable-type="\App\Modules\Buses\Models\BusRoute::class"
                    :leadable-id="$route->id"
                    :extras="['travel_date' => '', 'seats' => 1]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
