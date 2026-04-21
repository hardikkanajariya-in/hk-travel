<div class="container mx-auto px-4 py-10">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('activities.index') }}" class="hover:underline">Activities</a> /
        <span>{{ $activity->name }}</span>
    </nav>

    @if ($activity->cover_image)
        <img src="{{ $activity->cover_image }}" alt="" class="w-full aspect-[21/9] rounded-xl object-cover mb-8">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <header>
                <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ $activity->category }}</p>
                <h1 class="text-3xl font-semibold mt-1">{{ $activity->name }}</h1>
                <p class="text-zinc-500 mt-1">{{ $activity->duration_hours }} hours · {{ ucfirst($activity->difficulty) }} · Min age {{ $activity->min_age }}</p>
            </header>

            @if ($activity->short_description)
                <p class="text-lg text-zinc-700 dark:text-zinc-300">{{ $activity->short_description }}</p>
            @endif

            @if ($activity->description)
                <div class="prose dark:prose-invert max-w-none">{!! $activity->description !!}</div>
            @endif

            @if (! empty($activity->highlights))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Highlights</h2>
                    <ul class="space-y-2 text-sm">
                        @foreach ($activity->highlights as $h)
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $h }}</li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            @if (! empty($activity->included))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">What's included</h2>
                    <ul class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ($activity->included as $i)
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $i }}</li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">From</p>
                <p class="text-3xl font-semibold">{{ $activity->currency }} {{ number_format((float) $activity->price, 0) }}</p>
                <p class="text-xs text-zinc-500">per person</p>
            </x-ui.card>

            <x-ui.card>
                <h2 class="font-semibold mb-3">Book this experience</h2>
                <livewire:hk.enquiry-form
                    source="activity"
                    :leadable-type="\App\Modules\Activities\Models\Activity::class"
                    :leadable-id="$activity->id"
                    :extras="['preferred_date' => '', 'guests' => 2]"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
