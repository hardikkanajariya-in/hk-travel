@php
    $align = match ($data['align'] ?? 'center') {
        'left' => 'mr-auto', 'right' => 'ml-auto', default => 'mx-auto',
    };
    $rounded = ($data['rounded'] ?? true) ? 'rounded-xl' : '';
@endphp

@if (! empty($data['url']))
    <figure class="space-y-2">
        <img src="{{ $data['url'] }}" alt="{{ $data['alt'] ?? '' }}" loading="lazy" decoding="async"
             class="block max-w-full {{ $rounded }} {{ $align }}">
        @if (! empty($data['caption']))
            <figcaption class="text-center text-sm text-zinc-500">{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
