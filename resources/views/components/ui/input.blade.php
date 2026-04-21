@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'iconLeading' => null,
    'iconTrailing' => null,
])

@php
    $inputId = $id ?? $name ?? 'input-'.\Illuminate\Support\Str::random(6);
    $hasError = filled($error);
    $describedBy = collect([
        $hasError ? "$inputId-error" : null,
        filled($hint) ? "$inputId-hint" : null,
    ])->filter()->implode(' ');

    $inputClass = implode(' ', [
        'block w-full rounded-xl border bg-white dark:bg-zinc-900',
        'text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 dark:placeholder:text-zinc-500',
        'px-3.5 py-2.5 text-sm shadow-sm',
        'transition-[border-color,box-shadow,background-color] duration-200 ease-out',
        'hover:border-zinc-400 dark:hover:border-zinc-600',
        'focus:outline-none focus:ring-4 focus:ring-offset-0',
        'disabled:bg-zinc-50 disabled:text-zinc-500 disabled:cursor-not-allowed dark:disabled:bg-zinc-800',
    ]);
    $inputClass .= $hasError
        ? ' border-hk-danger focus:ring-hk-danger/15 focus:border-hk-danger'
        : ' border-zinc-300 dark:border-zinc-700 focus:ring-hk-primary-500/15 focus:border-hk-primary-500';
    if ($iconLeading)  { $inputClass .= ' pl-10'; }
    if ($iconTrailing) { $inputClass .= ' pr-10'; }
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($iconLeading)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400">
                <x-dynamic-component :component="'heroicon-'.$iconLeading" class="size-4" />
            </span>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            @if ($name) name="{{ $name }}" @endif
            @if (!is_null($value)) value="{{ $value }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes->class($inputClass) }}
        />

        @if ($iconTrailing)
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                <x-dynamic-component :component="'heroicon-'.$iconTrailing" class="size-4" />
            </span>
        @endif
    </div>

    @if (filled($hint) && !$hasError)
        <p id="{{ $inputId }}-hint" class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p id="{{ $inputId }}-error" class="text-xs text-hk-danger" role="alert">{{ $error }}</p>
    @endif
</div>
