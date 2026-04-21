<div>
    @php $schema = $post->toSeoMeta()['schema'] ?? null; @endphp
    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <article>
        @if ($post->cover_image)
            <div class="aspect-[16/6] bg-zinc-100 dark:bg-zinc-800">
                <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
            </div>
        @endif

        <header class="mx-auto max-w-3xl px-6 pt-10">
            <nav class="mb-3 text-xs text-zinc-500">
                <a href="{{ route('blog.index') }}" wire:navigate class="hover:underline">{{ __('blog::blog.title') }}</a>
                <span class="mx-1">/</span>
                <span>{{ $post->title }}</span>
            </nav>
            <div class="mb-3 flex flex-wrap gap-1">
                @foreach ($post->categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" wire:navigate><x-ui.badge>{{ $cat->name }}</x-ui.badge></a>
                @endforeach
            </div>
            <h1 class="text-3xl font-bold leading-tight md:text-4xl">{{ $post->title }}</h1>
            <p class="mt-3 text-sm text-zinc-500">
                {{ __('blog::blog.published_on', ['date' => $post->published_at?->format('M d, Y')]) }}
                @if ($post->author) · {{ __('blog::blog.by_author', ['name' => $post->author->name]) }} @endif
                · {{ trans_choice('blog::blog.reading_minutes', $post->reading_minutes ?: 1, ['count' => $post->reading_minutes ?: 1]) }}
            </p>
        </header>

        <div class="mx-auto mt-8 max-w-7xl px-6 pb-16">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">
                <div class="prose prose-zinc max-w-none dark:prose-invert lg:col-span-3">
                    @if ($post->excerpt)
                        <p class="lead text-lg text-zinc-600 dark:text-zinc-400">{{ $post->excerpt }}</p>
                    @endif
                    {!! $bodyHtml !!}

                    @if ($post->tags->isNotEmpty())
                        <hr>
                        <p class="not-prose flex flex-wrap items-center gap-1 text-sm">
                            <span class="text-zinc-500">{{ __('blog::blog.tags') }}:</span>
                            @foreach ($post->tags as $t)
                                <a href="{{ route('blog.tag', $t->slug) }}" wire:navigate class="rounded bg-zinc-100 px-2 py-0.5 text-xs hover:bg-zinc-200 dark:bg-zinc-800">{{ $t->name }}</a>
                            @endforeach
                        </p>
                    @endif
                </div>

                <aside class="space-y-6">
                    @if ($post->show_toc && count($toc) > 0)
                        <x-ui.card class="p-5 lg:sticky lg:top-24">
                            <h3 class="mb-3 text-sm font-semibold uppercase text-zinc-500">{{ __('blog::blog.toc') }}</h3>
                            <ul class="space-y-1 text-sm">
                                @foreach ($toc as $item)
                                    <li class="@if ($item['level'] === 3) ml-4 text-xs @endif"><a href="#{{ $item['id'] }}" class="text-zinc-700 hover:underline dark:text-zinc-300">{{ $item['text'] }}</a></li>
                                @endforeach
                            </ul>
                        </x-ui.card>
                    @endif
                </aside>
            </div>

            @if ($related->isNotEmpty())
                <section class="mt-16">
                    <h2 class="mb-6 text-2xl font-semibold">{{ __('blog::blog.related') }}</h2>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        @foreach ($related as $r)
                            <a href="{{ route('blog.show', $r->slug) }}" wire:navigate class="group block overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                                @if ($r->cover_image)
                                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                                        <img src="{{ $r->cover_image }}" alt="{{ $r->title }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="line-clamp-2 font-semibold leading-snug group-hover:underline">{{ $r->title }}</h3>
                                    <p class="mt-1 text-xs text-zinc-500">{{ $r->published_at?->format('M d, Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($post->allow_comments && config('hk-modules.modules.comments.enabled'))
                <section class="mx-auto mt-16 max-w-3xl">
                    <livewire:comments-public.comment-section :commentable="$post" />
                </section>
            @endif
        </div>
    </article>
</div>
