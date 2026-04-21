@php
    $align = match ($data['align'] ?? 'center') {
        'left' => 'text-left items-start',
        'right' => 'text-right items-end',
        default => 'text-center items-center',
    };
    $overlay = match ($data['overlay'] ?? 'dark') {
        'light' => 'bg-white/40',
        'dark' => 'bg-black/50',
        default => '',
    };
@endphp

<section class="relative isolate overflow-hidden rounded-2xl">
    @if (! empty($data['image']))
        <img src="{{ $data['image'] }}" alt="" loading="lazy" decoding="async"
             class="absolute inset-0 -z-10 h-full w-full object-cover">
        @if ($overlay)
            <div class="absolute inset-0 -z-10 {{ $overlay }}"></div>
        @endif
    @else
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-hk-primary-600 to-hk-primary-800"></div>
    @endif

    <div class="mx-auto flex max-w-5xl flex-col gap-5 px-6 py-24 sm:py-32 {{ $align }}
                {{ ! empty($data['image']) ? 'text-white' : 'text-white' }}">
        @if (! empty($data['eyebrow']))
            <p class="text-sm font-semibold uppercase tracking-widest opacity-80">{{ $data['eyebrow'] }}</p>
        @endif
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
            {{ $data['heading'] ?? '' }}
        </h1>
        @if (! empty($data['subheading']))
            <p class="text-lg sm:text-xl max-w-2xl opacity-90">{{ $data['subheading'] }}</p>
        @endif

        @if (! empty($data['cta_label']) || ! empty($data['cta2_label']))
            <div class="mt-2 flex flex-wrap gap-3">
                @if (! empty($data['cta_label']))
                    <a href="{{ $data['cta_url'] ?? '#' }}"
                       class="inline-flex items-center rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-hk-primary-700 shadow hover:bg-zinc-50 transition">
                        {{ $data['cta_label'] }}
                    </a>
                @endif
                @if (! empty($data['cta2_label']))
                    <a href="{{ $data['cta2_url'] ?? '#' }}"
                       class="inline-flex items-center rounded-md border border-white/40 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition">
                        {{ $data['cta2_label'] }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
