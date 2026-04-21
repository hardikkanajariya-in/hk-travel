@php
    /** @var array<string, mixed> $data */
    $slug = $data['slug'] ?? null;
@endphp

<section class="mx-auto max-w-2xl space-y-6 px-4 py-10 sm:px-6">
    @if (! empty($data['heading']))
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif

    @if (! empty($data['intro']))
        <div class="prose prose-zinc max-w-none dark:prose-invert">{!! $data['intro'] !!}</div>
    @endif

    @if ($slug)
        <livewire:dynamic-component :component="'pages::public.contact-form'" :slug="$slug" :key="'cf-'.$slug" />
    @else
        <p class="text-sm text-zinc-500">No form selected.</p>
    @endif
</section>
