@php
    $url = $data['url'] ?? null;
    $aspect = match ($data['aspect'] ?? '16/9') {
        '4/3' => 'aspect-[4/3]', '1/1' => 'aspect-square', default => 'aspect-video',
    };

    $embedUrl = null;
    if ($url) {
        if (preg_match('#youtube\.com/watch\?v=([\w-]+)#', $url, $m) || preg_match('#youtu\.be/([\w-]+)#', $url, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/'.$m[1];
        } elseif (preg_match('#vimeo\.com/(\d+)#', $url, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/'.$m[1];
        }
    }
@endphp

@if ($embedUrl)
    <figure class="space-y-2">
        <div class="{{ $aspect }} w-full overflow-hidden rounded-lg bg-black">
            <iframe src="{{ $embedUrl }}"
                    class="h-full w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
        @if (! empty($data['caption']))
            <figcaption class="text-center text-sm text-zinc-500">{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
@endif
