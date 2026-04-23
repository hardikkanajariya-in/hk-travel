<div class="space-y-6">
    <div class="flex items-end justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-semibold">{{ __('reviews::reviews.plural') }}</h2>
            @if ($count > 0)
                <p class="text-sm text-zinc-500 mt-1">
                    <span class="text-2xl text-amber-400 align-middle">★</span>
                    <span class="text-2xl font-bold align-middle">{{ number_format($avg, 1) }}</span>
                    <span class="ml-1">/ 5 · {{ $count }} {{ \Illuminate\Support\Str::plural('review', $count) }}</span>
                </p>
            @endif
        </div>

        @if ($count > 0)
            <x-ui.select
                wire:model.live="sort"
                :options="\App\Core\Support\Choices::reviewSortOptions()"
            />
        @endif
    </div>

    @if ($count > 0)
        <div class="grid grid-cols-5 gap-1.5">
            @foreach ($distribution as $star => $n)
                @php $pct = $count > 0 ? round(($n / $count) * 100) : 0; @endphp
                <div class="flex items-center gap-2 text-xs col-span-5">
                    <span class="w-8">{{ $star }}★</span>
                    <div class="flex-1 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                        <div class="h-full bg-amber-400" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="w-10 text-right text-zinc-500">{{ $n }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if ($reviews->isEmpty())
        <p class="text-sm text-zinc-500 italic">{{ __('reviews::reviews.no_reviews') }}</p>
    @else
        <div class="space-y-5">
            @foreach ($reviews as $review)
                <article class="border-b border-zinc-200 dark:border-zinc-800 pb-5 last:border-0" wire:key="r-{{ $review->id }}">
                    <header class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium">{{ $review->authorName() }}
                                @if ($review->is_verified)
                                    <span class="text-[10px] text-hk-success ml-1">✓ verified</span>
                                @endif
                            </p>
                            <p class="text-xs text-zinc-500">{{ $review->approved_at?->diffForHumans() }}</p>
                        </div>
                        <span class="text-amber-500 whitespace-nowrap">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= round((float) $review->rating) ? '' : 'text-zinc-300 dark:text-zinc-700' }}">★</span>
                            @endfor
                        </span>
                    </header>

                    @if ($review->title)
                        <h3 class="mt-2 font-semibold">{{ $review->title }}</h3>
                    @endif
                    <p class="mt-1.5 text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $review->body }}</p>

                    @if (! empty($review->criteria))
                        <div class="mt-3 flex flex-wrap gap-3 text-xs text-zinc-500">
                            @foreach ((array) $review->criteria as $k => $v)
                                <span><span class="font-medium text-zinc-700 dark:text-zinc-300">{{ __('reviews::reviews.criteria.'.$k) }}</span>: {{ number_format((float) $v, 1) }}/5</span>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        <div>{{ $reviews->links() }}</div>
    @endif
</div>
