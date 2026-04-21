@php
    $variant = $data['variant'] ?? 'primary';
    $bg = $variant === 'primary'
        ? 'bg-hk-primary-600 text-white'
        : 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100';
@endphp

<section class="rounded-2xl {{ $bg }} px-6 py-12 sm:px-12 sm:py-16 text-center">
    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ $data['heading'] ?? '' }}</h2>
    @if (! empty($data['subheading']))
        <p class="mx-auto mt-3 max-w-2xl text-base opacity-90">{{ $data['subheading'] }}</p>
    @endif
    @if (! empty($data['cta_label']))
        <a href="{{ $data['cta_url'] ?? '#' }}"
           class="mt-6 inline-flex items-center rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow hover:bg-zinc-50 transition">
            {{ $data['cta_label'] }}
        </a>
    @endif
</section>
