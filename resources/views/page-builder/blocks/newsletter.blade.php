<section class="rounded-2xl bg-zinc-50 dark:bg-zinc-900 px-6 py-10 sm:px-10 sm:py-12">
    <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-2xl font-semibold tracking-tight">{{ $data['heading'] ?? '' }}</h2>
        @if (! empty($data['subheading']))
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $data['subheading'] }}</p>
        @endif

        <form action="#" method="POST" class="mt-6 flex flex-col gap-3 sm:flex-row">
            @csrf
            <input type="email" required name="email"
                   placeholder="{{ $data['placeholder'] ?? 'you@example.com' }}"
                   class="flex-1 rounded-md border border-zinc-300 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-hk-primary-500 focus:outline-none focus:ring-2 focus:ring-hk-primary-500/30 dark:border-zinc-700 dark:bg-zinc-950">
            <x-ui.captcha />
            <button type="submit"
                    class="rounded-md bg-hk-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-hk-primary-700 transition">
                {{ $data['cta_label'] ?? 'Subscribe' }}
            </button>
        </form>
    </div>
</section>
