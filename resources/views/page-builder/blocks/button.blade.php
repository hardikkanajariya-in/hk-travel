@php
    $align = match ($data['align'] ?? 'left') {
        'center' => 'justify-center', 'right' => 'justify-end', default => 'justify-start',
    };
@endphp

<div class="flex {{ $align }}">
    <x-ui.button :href="$data['url'] ?? '#'" :variant="$data['variant'] ?? 'primary'" :size="$data['size'] ?? 'md'">
        {{ $data['label'] ?? '' }}
    </x-ui.button>
</div>
