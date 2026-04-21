@php
    $grid = match ($data['layout'] ?? '1-1') {
        '1-2' => 'lg:grid-cols-3 [&>*:first-child]:lg:col-span-1 [&>*:last-child]:lg:col-span-2',
        '2-1' => 'lg:grid-cols-3 [&>*:first-child]:lg:col-span-2 [&>*:last-child]:lg:col-span-1',
        default => 'lg:grid-cols-2',
    };
@endphp

<div class="grid grid-cols-1 {{ $grid }} gap-6">
    <div class="prose prose-zinc dark:prose-invert max-w-none">{!! $data['left_html'] ?? '' !!}</div>
    <div class="prose prose-zinc dark:prose-invert max-w-none">{!! $data['right_html'] ?? '' !!}</div>
</div>
