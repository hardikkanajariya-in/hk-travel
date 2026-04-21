<section class="space-y-6">
    @if (! empty($data['title']))
        <h2 class="text-2xl font-semibold tracking-tight">{{ $data['title'] }}</h2>
    @endif

    <div class="divide-y divide-zinc-200 rounded-xl border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800"
         x-data="{ open: null }">
        @foreach ($data['items'] ?? [] as $i => $item)
            <div>
                <button type="button"
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        :aria-expanded="open === {{ $i }}"
                        class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-base font-medium hover:bg-zinc-50 dark:hover:bg-zinc-900">
                    <span>{{ $item['q'] ?? '' }}</span>
                    <svg :class="open === {{ $i }} ? 'rotate-180' : ''"
                         class="size-5 shrink-0 text-zinc-400 transition" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 011.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                              clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse class="prose prose-zinc dark:prose-invert max-w-none px-5 pb-5">
                    {!! $item['a'] ?? '' !!}
                </div>
            </div>
        @endforeach
    </div>
</section>
