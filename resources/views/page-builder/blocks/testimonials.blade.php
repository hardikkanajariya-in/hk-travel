<section class="space-y-6">
    @if (! empty($data['title']))
        <h2 class="text-2xl font-semibold tracking-tight text-center">{{ $data['title'] }}</h2>
    @endif

    @if (! empty($data['items']))
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($data['items'] as $t)
                <figure class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <blockquote class="text-zinc-700 dark:text-zinc-300">“{{ $t['quote'] ?? '' }}”</blockquote>
                    <figcaption class="mt-4 flex items-center gap-3">
                        @if (! empty($t['avatar']))
                            <img src="{{ $t['avatar'] }}" alt="" loading="lazy" class="size-10 rounded-full object-cover">
                        @endif
                        <div>
                            <div class="text-sm font-semibold">{{ $t['author'] ?? '' }}</div>
                            @if (! empty($t['role']))
                                <div class="text-xs text-zinc-500">{{ $t['role'] }}</div>
                            @endif
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    @endif
</section>
