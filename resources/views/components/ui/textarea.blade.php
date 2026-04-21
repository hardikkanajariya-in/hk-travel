@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 4,
])

{{--
    HK Travel base textarea. Single source of truth — every multi-line
    input in the app should render through this component so that any
    later swap (e.g. monospace, autosize) is a one-file change.
--}}

@php
    $textareaId = $id ?? $name ?? 'textarea-'.\Illuminate\Support\Str::random(6);
    $hasError = filled($error);
    $describedBy = collect([
        $hasError ? "$textareaId-error" : null,
        filled($hint) ? "$textareaId-hint" : null,
    ])->filter()->implode(' ');

    $textareaClass = 'block w-full rounded-md border bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 px-3 py-2 text-sm shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:bg-zinc-50 disabled:text-zinc-500 dark:disabled:bg-zinc-800';
    $textareaClass .= $hasError
        ? ' border-hk-danger focus:ring-hk-danger/30 focus:border-hk-danger'
        : ' border-zinc-300 dark:border-zinc-700 focus:ring-hk-primary-500/30 focus:border-hk-primary-500';
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $textareaId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        @if ($name) name="{{ $name }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->class($textareaClass) }}
    >{{ $value ?? $slot }}</textarea>

    @if (filled($hint) && ! $hasError)
        <p id="{{ $textareaId }}-hint" class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p id="{{ $textareaId }}-error" class="text-xs text-hk-danger">{{ $error }}</p>
    @endif
</div>
