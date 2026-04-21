<div>
    <section class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/40">
        <div class="mx-auto max-w-7xl px-6 py-10">
            <h1 class="text-3xl font-bold md:text-4xl">
                @if ($category) {{ __('blog::blog.category') }}: {{ $category->name }}
                @elseif ($tag) {{ __('blog::blog.tag') }}: {{ $tag->name }}
                @else {{ __('blog::blog.title') }}
                @endif
            </h1>
            <p class="mt-2 text-sm text-zinc-500">
                @if ($category) {{ $category->description }}
                @else {{ __('blog::blog.tagline') }}
                @endif
            </p>
            <link rel="alternate" type="application/rss+xml" title="RSS" href="{{ route('blog.rss') }}" />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
            <div class="lg:col-span-3">
                <div class="mb-6 flex items-center justify-between gap-3">
                    <x-ui.input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search posts...') }}" class="max-w-sm" />
                    <a href="{{ route('blog.rss') }}" class="text-sm text-zinc-500 hover:underline">{{ __('blog::blog.rss') }}</a>
                </div>

                @if ($posts->isEmpty())
                    <x-ui.alert>{{ __('blog::blog.no_posts') }}</x-ui.alert>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @foreach ($posts as $post)
                            <article class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                                <a href="{{ route('blog.show', $post->slug) }}" wire:navigate>
                                    @if ($post->cover_image)
                                        <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                                            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="h-full w-full object-cover" loading="lazy">
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <div class="mb-2 flex flex-wrap gap-1">
                                            @foreach ($post->categories->take(2) as $cat)
                                                <x-ui.badge>{{ $cat->name }}</x-ui.badge>
                                            @endforeach
                                        </div>
                                        <h2 class="text-lg font-semibold leading-snug">{{ $post->title }}</h2>
                                        @if ($post->excerpt)
                                            <p class="mt-2 line-clamp-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $post->excerpt }}</p>
                                        @endif
                                        <p class="mt-3 text-xs text-zinc-500">
                                            {{ $post->published_at?->format('M d, Y') }} · {{ trans_choice('blog::blog.reading_minutes', $post->reading_minutes ?: 1, ['count' => $post->reading_minutes ?: 1]) }}
                                        </p>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $posts->links() }}</div>
                @endif
            </div>

            <aside class="space-y-6">
                <x-ui.card class="p-5">
                    <h3 class="mb-3 text-sm font-semibold uppercase text-zinc-500">{{ __('blog::blog.categories') }}</h3>
                    <ul class="space-y-1 text-sm">
                        @foreach ($categories as $cat)
                            <li><a href="{{ route('blog.category', $cat->slug) }}" wire:navigate class="hover:underline">{{ $cat->name }} <span class="text-xs text-zinc-400">({{ $cat->posts_count }})</span></a></li>
                        @endforeach
                    </ul>
                </x-ui.card>
                <x-ui.card class="p-5">
                    <h3 class="mb-3 text-sm font-semibold uppercase text-zinc-500">{{ __('blog::blog.tags') }}</h3>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($popularTags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" wire:navigate class="rounded bg-zinc-100 px-2 py-0.5 text-xs hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </x-ui.card>
            </aside>
        </div>
    </section>
</div>
