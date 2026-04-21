<div class="container mx-auto px-4 py-10 max-w-5xl">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('taxi.index') }}" class="hover:underline">Taxi</a> /
        <span>{{ $service->title }}</span>
    </nav>

    <header class="mb-6">
        <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ str_replace('_', ' ', $service->service_type) }}</p>
        <h1 class="text-3xl font-semibold mt-1">{{ $service->title }}</h1>
        <p class="text-zinc-500 mt-1">{{ $service->vehicle_type }} · {{ $service->capacity }} passengers · {{ $service->luggage }} luggage</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            @if ($service->description)
                <div class="prose dark:prose-invert max-w-none">{!! $service->description !!}</div>
            @endif

            @if (! empty($service->features))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Included</h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        @foreach ($service->features as $f)<li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $f }}</li>@endforeach
                    </ul>
                </x-ui.card>
            @endif

            <x-ui.card>
                <h2 class="font-semibold mb-3">Pricing</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div><dt class="text-zinc-500">Base fare</dt><dd class="font-semibold">{{ $service->currency }} {{ number_format((float) $service->base_fare, 2) }}</dd></div>
                    <div><dt class="text-zinc-500">Per km</dt><dd class="font-semibold">{{ $service->currency }} {{ number_format((float) $service->per_km_rate, 2) }}</dd></div>
                    <div><dt class="text-zinc-500">Per hour</dt><dd class="font-semibold">{{ $service->currency }} {{ number_format((float) $service->per_hour_rate, 2) }}</dd></div>
                    <div><dt class="text-zinc-500">Flat rate</dt><dd class="font-semibold">{{ $service->currency }} {{ number_format((float) $service->flat_rate, 2) }}</dd></div>
                </dl>
            </x-ui.card>
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <h2 class="font-semibold mb-3">Book a transfer</h2>
                <livewire:hk.enquiry-form
                    source="taxi"
                    :leadable-type="\App\Modules\Taxi\Models\TaxiService::class"
                    :leadable-id="$service->id"
                    :extras="['pickup' => '', 'dropoff' => '', 'pickup_datetime' => '', 'passengers' => 2]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
