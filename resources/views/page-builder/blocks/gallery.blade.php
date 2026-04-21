@php
    $cols = (int) ($data['columns'] ?? 3);
    $grid = match ($cols) {
        2 => 'sm:grid-cols-2',
        4 => 'sm:grid-cols-2 lg:grid-cols-4',
        default => 'sm:grid-cols-2 lg:grid-cols-3',
    };
    $images = $data['images'] ?? [];
@endphp

@if (! empty($images))
    <div class="grid grid-cols-1 {{ $grid }} gap-3">
        @foreach ($images as $img)
            <img src="{{ $img['url'] ?? '' }}"
                 alt="{{ $img['alt'] ?? '' }}"
                 loading="lazy" decoding="async"
                 class="aspect-square w-full rounded-lg object-cover">
        @endforeach
    </div>
@endif
