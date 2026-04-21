@php
    $tag = in_array($data['level'] ?? 'h2', ['h1', 'h2', 'h3', 'h4'], true) ? $data['level'] : 'h2';
    $align = match ($data['align'] ?? 'left') {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
    $size = match ($tag) {
        'h1' => 'text-4xl sm:text-5xl font-bold tracking-tight',
        'h2' => 'text-3xl sm:text-4xl font-semibold tracking-tight',
        'h3' => 'text-2xl font-semibold',
        'h4' => 'text-xl font-semibold',
    };
@endphp

<{{ $tag }} class="{{ $size }} {{ $align }} text-zinc-900 dark:text-zinc-100">
    {{ $data['text'] ?? '' }}
</{{ $tag }}>
