<div class="mx-auto max-w-4xl px-6 py-12">
    <h1 class="text-3xl font-bold">{{ $tour->name }}</h1>
    <p class="mt-2 text-zinc-500">{{ $tour->duration_days }} days · max {{ $tour->max_group_size }} guests</p>
    <p class="mt-4 text-2xl font-bold">{{ number_format($tour->price, 2) }}</p>

    <div class="prose dark:prose-invert mt-8">
        {!! nl2br(e($tour->description)) !!}
    </div>
</div>
