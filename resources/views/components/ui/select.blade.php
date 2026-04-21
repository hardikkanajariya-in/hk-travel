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
    'searchable' => false,
])

@php
    $selectId = $id ?? $name ?? 'select-'.\Illuminate\Support\Str::random(6);

    // Auto-pull validation errors from the $errors bag (same behaviour as
    // x-ui.input) so admin forms that wire:model their fields don't have to
    // pass :error="$errors->first(...)" on every component.
    $wireKey = $attributes->wire('model')->value();
    $errorKey = $name ?? $wireKey;
    if (blank($error) && filled($errorKey) && isset($errors) && $errors->has($errorKey)) {
        $error = $errors->first($errorKey);
    }
    $hasError = filled($error);

    $describedBy = collect([
        $hasError ? "$selectId-error" : null,
        filled($hint) ? "$selectId-hint" : null,
    ])->filter()->implode(' ');

    // Auto-enable search when the option list is large enough to warrant it.
    $autoSearchable = $searchable || (is_array($options) && count($options) > 12);

    $triggerClass = implode(' ', [
        'group relative flex w-full cursor-pointer items-center justify-between rounded-xl border bg-white dark:bg-zinc-900',
        'text-zinc-900 dark:text-zinc-100',
        'pl-3.5 pr-3 py-2.5 text-sm shadow-sm text-left',
        'transition-[border-color,box-shadow,background-color] duration-200 ease-out',
        'hover:border-zinc-400 dark:hover:border-zinc-600',
        'focus:outline-none focus:ring-4 focus:ring-offset-0',
        'disabled:bg-zinc-50 disabled:text-zinc-500 disabled:cursor-not-allowed dark:disabled:bg-zinc-800',
    ]);
    $triggerClass .= $hasError
        ? ' border-hk-danger focus:ring-hk-danger/15 focus:border-hk-danger'
        : ' border-zinc-300 dark:border-zinc-700 focus:ring-hk-primary-500/15 focus:border-hk-primary-500';

    // Strip our own props from the attributes that get spread onto the
    // hidden native <select> (which carries wire:model + change events).
    $nativeAttributes = $attributes->except(['class']);
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $selectId }}-trigger" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div
        x-data="hkSelect({
            initial: @js((string) ($value ?? '')),
            options: @js($options),
            placeholder: @js($placeholder),
            searchable: @js((bool) $autoSearchable),
        })"
        x-on:click.outside="close()"
        x-on:keydown.escape.window="close()"
        class="relative"
    >
        {{-- Hidden native <select> keeps wire:model integration intact and
             remains the source of truth for forms / a11y fallbacks. --}}
        <select
            x-ref="native"
            id="{{ $selectId }}"
            @if ($name) name="{{ $name }}" @endif
            @disabled($disabled)
            @if ($required) required @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            tabindex="-1"
            aria-hidden="true"
            class="sr-only"
            {{ $nativeAttributes }}
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

        <button
            type="button"
            id="{{ $selectId }}-trigger"
            x-ref="trigger"
            @click="toggle()"
            @keydown.arrow-down.prevent="open ? focusNext() : openPanel()"
            @keydown.arrow-up.prevent="open ? focusPrev() : openPanel()"
            @keydown.enter.prevent="open ? selectFocused() : openPanel()"
            :aria-expanded="open ? 'true' : 'false'"
            aria-haspopup="listbox"
            @disabled($disabled)
            class="{{ $triggerClass }}"
        >
            <span
                class="block truncate"
                :class="value === '' ? 'text-zinc-400 dark:text-zinc-500' : ''"
                x-text="currentLabel()"
            ></span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                 class="ml-2 size-4 shrink-0 text-zinc-400 transition-transform duration-200"
                 :class="open ? 'rotate-180 text-hk-primary-500' : ''">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
            class="absolute left-0 right-0 z-40 mt-1.5 origin-top overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-lg ring-1 ring-black/5 dark:border-zinc-700 dark:bg-zinc-900 dark:ring-white/10"
        >
            <template x-if="searchable">
                <div class="border-b border-zinc-100 dark:border-zinc-800 p-2">
                    <input
                        x-ref="search"
                        x-model="query"
                        @keydown.arrow-down.prevent="focusNext()"
                        @keydown.arrow-up.prevent="focusPrev()"
                        @keydown.enter.prevent="selectFocused()"
                        type="text"
                        placeholder="{{ __('ui.search.placeholder') }}"
                        class="block w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-xs text-zinc-900 placeholder:text-zinc-400 focus:border-hk-primary-500 focus:outline-none focus:ring-2 focus:ring-hk-primary-500/15 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    >
                </div>
            </template>

            <ul
                role="listbox"
                class="hk-select-panel max-h-60 overflow-y-auto py-1 text-sm"
            >
                <template x-for="(opt, idx) in filtered()" :key="opt.value">
                    <li
                        role="option"
                        :aria-selected="value === opt.value ? 'true' : 'false'"
                        @click="select(opt.value)"
                        @mouseenter="focusedIndex = idx"
                        :class="{
                            'bg-hk-primary-600 text-white': focusedIndex === idx,
                            'text-hk-primary-700 dark:text-hk-primary-300 font-semibold': value === opt.value && focusedIndex !== idx,
                            'text-zinc-700 dark:text-zinc-200': value !== opt.value && focusedIndex !== idx,
                        }"
                        class="flex cursor-pointer items-center justify-between gap-2 px-3 py-2 transition-colors"
                    >
                        <span class="block truncate" x-text="opt.label"></span>
                        <svg
                            x-show="value === opt.value"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="size-4 shrink-0"
                        >
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </li>
                </template>
                <li x-show="filtered().length === 0"
                    class="px-3 py-3 text-center text-xs text-zinc-400">
                    {{ __('ui.empty') }}
                </li>
            </ul>
        </div>
    </div>

    @if ($hasError)
        <p id="{{ $selectId }}-error" class="text-xs text-hk-danger" role="alert">{{ $error }}</p>
    @elseif (filled($hint))
        <p id="{{ $selectId }}-hint" class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
</div>
