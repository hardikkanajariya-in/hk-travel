@php
    $h = match ($data['size'] ?? 'md') {
        'sm' => 'h-4', 'lg' => 'h-16', 'xl' => 'h-24', default => 'h-8',
    };
@endphp

<div class="{{ $h }}" aria-hidden="true"></div>
