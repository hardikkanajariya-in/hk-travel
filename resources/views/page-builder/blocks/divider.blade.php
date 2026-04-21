@php
    $style = match ($data['style'] ?? 'solid') {
        'dashed' => 'border-dashed', 'dotted' => 'border-dotted', default => 'border-solid',
    };
@endphp

<hr class="my-2 border-t {{ $style }} border-zinc-200 dark:border-zinc-800">
