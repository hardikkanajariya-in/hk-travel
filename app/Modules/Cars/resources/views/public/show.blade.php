<div class="container mx-auto px-4 py-10">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('cars.index') }}" class="hover:underline">Cars</a> /
        <span>{{ $car->name }}</span>
    </nav>

    @if ($car->cover_image)
        <img src="{{ $car->cover_image }}" alt="" class="w-full aspect-[21/9] rounded-xl object-cover mb-8">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <header>
                <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ ucfirst($car->vehicle_class) }}</p>
                <h1 class="text-3xl font-semibold mt-1">{{ $car->name }}</h1>
            </header>

            @if ($car->description)
                <div class="prose dark:prose-invert max-w-none">{!! $car->description !!}</div>
            @endif

            <x-ui.card>
                <h2 class="font-semibold mb-3">Specifications</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div><dt class="text-zinc-500">Seats</dt><dd class="font-semibold">{{ $car->seats }}</dd></div>
                    <div><dt class="text-zinc-500">Doors</dt><dd class="font-semibold">{{ $car->doors }}</dd></div>
                    <div><dt class="text-zinc-500">Luggage</dt><dd class="font-semibold">{{ $car->luggage }}</dd></div>
                    <div><dt class="text-zinc-500">Transmission</dt><dd class="font-semibold">{{ ucfirst($car->transmission) }}</dd></div>
                    <div><dt class="text-zinc-500">Fuel</dt><dd class="font-semibold">{{ ucfirst($car->fuel_type) }}</dd></div>
                    <div><dt class="text-zinc-500">A/C</dt><dd class="font-semibold">{{ $car->has_ac ? 'Yes' : 'No' }}</dd></div>
                </dl>
            </x-ui.card>

            @if (! empty($car->features))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Features</h2>
                    <ul class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ($car->features as $f)<li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $f }}</li>@endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">From</p>
                <p class="text-3xl font-semibold">{{ $car->currency }} {{ number_format((float) $car->daily_rate, 0) }}</p>
                <p class="text-xs text-zinc-500">per day</p>
                @if ((float) $car->weekly_rate > 0)
                    <p class="text-sm mt-2">Weekly: <span class="font-semibold">{{ $car->currency }} {{ number_format((float) $car->weekly_rate, 0) }}</span></p>
                @endif
            </x-ui.card>

            <x-ui.card>
                <h2 class="font-semibold mb-3">Reserve this car</h2>
                <livewire:hk.enquiry-form
                    source="car"
                    :leadable-type="\App\Modules\Cars\Models\CarRental::class"
                    :leadable-id="$car->id"
                    :extras="['pickup_date' => '', 'return_date' => '', 'pickup_location' => '', 'driver_age' => 30]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
