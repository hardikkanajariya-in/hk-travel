@if (! empty($data['url']))
    <figure class="space-y-2">
        <div class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-900">
            <iframe src="{{ $data['url'] }}"
                    class="h-full w-full"
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-presentation"></iframe>
        </div>
        @if (! empty($data['caption']))
            <figcaption class="text-center text-sm text-zinc-500">{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
