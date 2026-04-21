@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'label' => null,
    'checked' => false,
    'required' => false,
    'disabled' => false,
])

@php
    $checkboxId = $id ?? $name ?? 'checkbox-'.\Illuminate\Support\Str::random(6);
@endphp

<label for="{{ $checkboxId }}" class="inline-flex items-center gap-2 text-sm cursor-pointer select-none {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input
        type="checkbox"
        id="{{ $checkboxId }}"
        @if ($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        @if ($checked) checked @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        {{ $attributes->class('rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-hk-primary-600 focus:ring-2 focus:ring-hk-primary-500/30 focus:ring-offset-0 size-4 transition') }}
    />
    @if ($label)
        <span class="text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
    @endif
</label>
