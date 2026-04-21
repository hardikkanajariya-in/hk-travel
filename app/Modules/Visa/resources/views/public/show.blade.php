<div class="container mx-auto px-4 py-10 max-w-5xl">
    <nav class="text-xs text-zinc-500 mb-4">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('visa.index') }}" class="hover:underline">Visa</a> /
        <span>{{ $service->title }}</span>
    </nav>

    <header class="mb-6">
        <p class="text-sm uppercase tracking-wider text-hk-primary-600">{{ $service->country }} · {{ $service->visa_type }}</p>
        <h1 class="text-3xl font-semibold mt-1">{{ $service->title }}</h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            @if ($service->description)
                <div class="prose dark:prose-invert max-w-none">{!! $service->description !!}</div>
            @endif

            @if ($service->eligibility)
                <x-ui.card>
                    <h2 class="font-semibold mb-2">Eligibility</h2>
                    <p class="text-sm">{{ $service->eligibility }}</p>
                </x-ui.card>
            @endif

            @if (! empty($service->requirements))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Requirements</h2>
                    <ul class="space-y-2 text-sm">
                        @foreach ($service->requirements as $r)
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $r }}</li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            @if (! empty($service->documents))
                <x-ui.card>
                    <h2 class="font-semibold mb-3">Documents to provide</h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        @foreach ($service->documents as $d)
                            <li>• {{ $d }}</li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            @if ($service->notes)
                <x-ui.alert variant="info">{{ $service->notes }}</x-ui.alert>
            @endif
        </div>

        <aside class="space-y-4">
            <x-ui.card>
                <p class="text-sm text-zinc-500">Total from</p>
                <p class="text-3xl font-semibold">{{ $service->currency }} {{ number_format((float) $service->fee + (float) $service->service_fee, 0) }}</p>
                <p class="text-xs text-zinc-500 mt-1">Govt {{ number_format((float) $service->fee, 2) }} + service {{ number_format((float) $service->service_fee, 2) }}</p>
                <div class="mt-3 text-sm space-y-1">
                    <p>Processing: <span class="font-medium">{{ $service->processing_days_min }}–{{ $service->processing_days_max }} days</span></p>
                    <p>Stay: <span class="font-medium">{{ $service->allowed_stay_days }} days</span></p>
                    <p>Validity: <span class="font-medium">{{ $service->validity_days }} days</span></p>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="font-semibold mb-3">Apply for this visa</h2>
                <livewire:hk.enquiry-form
                    source="visa"
                    :leadable-type="\App\Modules\Visa\Models\VisaService::class"
                    :leadable-id="$service->id"
                    :extras="['nationality' => '', 'travel_date' => '']"
                />
            </x-ui.card>
        </aside>
    </div>
</div>
