@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'rows' => 8,
    'placeholder' => null,
    'profile' => 'rich-text',
])

{{--
    HK Travel rich-text editor — single source of truth so the underlying
    library can be swapped without touching call sites. The current
    implementation is a sanitised <x-ui.textarea> wrapped in an Alpine
    toolbar that inserts safe HTML snippets. Server-side every save MUST
    pass through App\Core\Security\Sanitizer with the matching profile
    (default `rich-text`) before persistence.
--}}

@php
    $editorId = $id ?? $name ?? 'rich-'.\Illuminate\Support\Str::random(6);
@endphp

<div
    x-data="{
        insert(html) {
            const el = document.getElementById('{{ $editorId }}');
            if (! el) { return; }
            const start = el.selectionStart ?? el.value.length;
            const end = el.selectionEnd ?? el.value.length;
            el.value = el.value.slice(0, start) + html + el.value.slice(end);
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.focus();
        }
    }"
    class="space-y-1.5"
>
    @if ($label)
        <label for="{{ $editorId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if ($required)<span class="text-hk-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="flex flex-wrap items-center gap-1 border-b border-zinc-200 dark:border-zinc-800 px-2 py-1.5 text-xs">
            <button type="button" @click="insert('<strong></strong>')" class="rounded px-2 py-1 font-bold text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">B</button>
            <button type="button" @click="insert('<em></em>')" class="rounded px-2 py-1 italic text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">I</button>
            <button type="button" @click="insert('<u></u>')" class="rounded px-2 py-1 underline text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">U</button>
            <span class="mx-1 h-4 w-px bg-zinc-200 dark:bg-zinc-700"></span>
            <button type="button" @click="insert('<h2></h2>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">H2</button>
            <button type="button" @click="insert('<h3></h3>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">H3</button>
            <button type="button" @click="insert('<p></p>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">P</button>
            <span class="mx-1 h-4 w-px bg-zinc-200 dark:bg-zinc-700"></span>
            <button type="button" @click="insert('<ul>\n  <li></li>\n</ul>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">{{ __('ui.richtext.list') }}</button>
            <button type="button" @click="insert('<ol>\n  <li></li>\n</ol>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">{{ __('ui.richtext.ordered_list') }}</button>
            <button type="button" @click="insert('<a href=&quot;https://&quot;></a>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">Link</button>
            <button type="button" @click="insert('<blockquote></blockquote>')" class="rounded px-2 py-1 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">“ Quote</button>
            <span class="ml-auto text-[10px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('ui.richtext.sanitize_notice') }}</span>
        </div>

        <textarea
            id="{{ $editorId }}"
            rows="{{ $rows }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            data-sanitize-profile="{{ $profile }}"
            {{ $attributes->class('block w-full resize-y bg-transparent px-3 py-3 font-mono text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none') }}
        >{{ $value ?? $slot }}</textarea>
    </div>

    @if (filled($hint) && blank($error))
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    @if (filled($error))
        <p class="text-xs text-hk-danger">{{ $error }}</p>
    @endif
</div>
