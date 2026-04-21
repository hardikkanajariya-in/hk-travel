@props(['variant' => 'blob', 'class' => ''])

{{-- Reusable decorative SVGs for the public theme. Pure CSS-coloured so
     they pick up the brand palette automatically. Each variant is meant to
     be absolutely positioned by the caller. --}}

@switch($variant)
    @case('blob-a')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <path fill="currentColor" d="M421.5 312.5q-22 62.5-78 99T215 460q-72-12-105.5-78T62 232q22-66 84-103t136-13q74 24 124 78.5t15.5 118Z"/>
        </svg>
        @break

    @case('blob-b')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <path fill="currentColor" d="M455 290q15 90-58 154T222 502q-89-12-138-86T48 234q34-82 116-115t172 5q90 38 119 166Z"/>
        </svg>
        @break

    @case('dots')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <defs>
                <pattern id="hk-dots" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.4" fill="currentColor"/>
                </pattern>
            </defs>
            <rect width="200" height="200" fill="url(#hk-dots)"/>
        </svg>
        @break

    @case('grid')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <defs>
                <pattern id="hk-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                    <path d="M32 0H0V32" fill="none" stroke="currentColor" stroke-width="0.6"/>
                </pattern>
            </defs>
            <rect width="200" height="200" fill="url(#hk-grid)"/>
        </svg>
        @break

    @case('wave')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none w-full '.$class]) }}>
            <path fill="currentColor" d="M0,80 C240,140 480,20 720,60 C960,100 1200,40 1440,80 L1440,120 L0,120 Z"/>
        </svg>
        @break

    @case('plane')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <path d="M6 38 58 12 46 56 36 40 24 50 22 36 6 38Z" fill="currentColor" opacity=".9"/>
        </svg>
        @break

    @case('compass')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="2"/>
            <path d="M32 12 38 32 32 52 26 32Z" fill="currentColor"/>
            <circle cx="32" cy="32" r="3" fill="currentColor"/>
        </svg>
        @break

    @default
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none '.$class]) }}>
            <path fill="currentColor" d="M427 281q-9 66-69 113t-138 38q-78-9-119-78T80 222q24-72 100-110t152 10q76 48 95 159Z"/>
        </svg>
@endswitch
