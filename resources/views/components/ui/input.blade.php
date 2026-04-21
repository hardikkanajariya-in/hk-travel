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

    // Auto-pull validation errors from the $errors bag using the wire:model
    // expression (or the raw `name` attribute) so callers don't need to pass
    // :error explicitly on every field.
    $wireKey = $attributes->wire('model')->value();
    $errorKey = $name ?? $wireKey;
    if (blank($error) && filled($errorKey) && isset($errors) && $errors->has($errorKey)) {
        $error = $errors->first($errorKey);
    }
    $hasError = filled($error);

    $isPassword = $type === 'password';

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
    if ($iconTrailing || $isPassword) { $inputClass .= ' pr-10'; }
@endphp

<div class="space-y-1.5"
    @if ($isPassword) x-data="{ visible: false }" @endif
>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($iconLeading)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400">
                <x-ui.icon :name="$iconLeading" class="size-4" />
            </span>
        @endif

        <input
            @if ($isPassword)
                x-bind:type="visible ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif
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

        @if ($isPassword)
            <button
                type="button"
                @click="visible = !visible"
                tabindex="-1"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-hk-primary-600 focus:outline-none focus:text-hk-primary-600 transition-colors"
                :aria-label="visible ? '{{ __('ui.password.hide') }}' : '{{ __('ui.password.show') }}'"
                :aria-pressed="visible ? 'true' : 'false'"
            >
                {{-- Eye (visible) --}}
                <svg x-show="!visible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{-- Eye-slash (hidden) --}}
                <svg x-show="visible" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
        @elseif ($iconTrailing)
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                <x-ui.icon :name="$iconTrailing" class="size-4" />
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
