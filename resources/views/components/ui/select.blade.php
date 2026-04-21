@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'options' => [],
])

@php
    $selectId = $id ?? $name ?? 'select-'.\Illuminate\Support\Str::random(6);
    $hasError = filled($error);
    $describedBy = collect([
        $hasError ? "$selectId-error" : null,
        filled($hint) ? "$selectId-hint" : null,
    ])->filter()->implode(' ');

    $selectClass = implode(' ', [
        'block w-full appearance-none cursor-pointer rounded-xl border bg-white dark:bg-zinc-900',
        'text-zinc-900 dark:text-zinc-100',
        'pl-3.5 pr-10 py-2.5 text-sm shadow-sm',
        'transition-[border-color,box-shadow,background-color] duration-200 ease-out',
        'hover:border-zinc-400 dark:hover:border-zinc-600',
        'focus:outline-none focus:ring-4 focus:ring-offset-0',
        'disabled:bg-zinc-50 disabled:text-zinc-500 disabled:cursor-not-allowed dark:disabled:bg-zinc-800',
    ]);
    $selectClass .= $hasError
        ? ' border-hk-danger focus:ring-hk-danger/15 focus:border-hk-danger'
        : ' border-zinc-300 dark:border-zinc-700 focus:ring-hk-primary-500/15 focus:border-hk-primary-500';
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $selectId }}"
            @if ($name) name="{{ $name }}" @endif
            @disabled($disabled)
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes->class($selectClass) }}
        >
            @if ($placeholder)
                <option value="" disabled @selected(blank($value))>{{ $placeholder }}</option>
            @endif

            @if (! empty($options))
                @foreach ($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
        </span>
    </div>

    @if ($hasError)
        <p id="{{ $selectId }}-error" class="text-xs text-hk-danger">{{ $error }}</p>
    @elseif (filled($hint))
        <p id="{{ $selectId }}-hint" class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
</div>
