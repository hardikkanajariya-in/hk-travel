<?php

use App\Models\ContactSubmission;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('My dashboard')] #[Layout('components.layouts.public')] class extends Component {
    public function with(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $email = $user->email;

        return [
            'user' => $user,
            'submissions' => ContactSubmission::query()
                ->with('form')
                ->where('email', $email)
                ->latest('id')
                ->limit(10)
                ->get(),
            'leads' => class_exists(Lead::class)
                ? Lead::query()
                    ->where(fn ($q) => $q->where('customer_id', $user->id)->orWhere('email', $email))
                    ->latest('id')
                    ->limit(5)
                    ->get()
                : collect(),
            'recentActivity' => method_exists($user, 'activities')
                ? $user->activities()->latest()->limit(8)->get()
                : collect(),
        ];
    }
};

?>

<div>
    @php
        app(\App\Core\Seo\BreadcrumbService::class)->push(__('Dashboard'), route('dashboard'));

        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->join('');

        $twoFactorEnabled = (bool) ($user->two_factor_secret ?? null);

        $exploreLinks = collect([
            ['route' => 'tours.index', 'label' => __('Browse tours')],
            ['route' => 'hotels.index', 'label' => __('Find hotels')],
            ['route' => 'activities.index', 'label' => __('Activities')],
        ])->filter(fn ($l) => \Illuminate\Support\Facades\Route::has($l['route']))->values();
    @endphp

    <div class="mx-auto w-full max-w-7xl px-6 pt-4 sm:pt-6">
        <x-theme.breadcrumbs />
    </div>

    <section class="mx-auto w-full max-w-7xl space-y-6 px-6 py-6 sm:space-y-8 sm:pt-4 sm:pb-10">
        {{-- Hero header --}}
        <header class="overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-hk-primary-50 via-white to-white p-5 sm:p-7 dark:border-zinc-800 dark:from-hk-primary-950/40 dark:via-zinc-900 dark:to-zinc-900">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-hk-primary-600 text-lg font-semibold text-white shadow-sm sm:size-16 sm:text-xl">
                        {{ $initials ?: '?' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-hk-primary-700 dark:text-hk-primary-300">{{ __('Welcome back') }}</p>
                        <h1 class="mt-1 truncate text-xl font-bold tracking-tight sm:text-2xl lg:text-3xl">{{ $user->name }}</h1>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Manage your account, requests, and recent activity.') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                    <a href="{{ route('profile.edit') }}" wire:navigate
                       class="inline-flex items-center gap-1.5 rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.06 2.06 0 1 1 2.915 2.914L7.5 18.677l-4 1 1-4L16.862 3.487Z"/></svg>
                        {{ __('Edit profile') }}
                    </a>
                    @if (\Illuminate\Support\Facades\Route::has('security.edit'))
                        <a href="{{ route('security.edit') }}" wire:navigate
                           class="inline-flex items-center gap-1.5 rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 5.385-4.365 9.75-9.75 9.75S1.5 17.385 1.5 12 5.865 2.25 11.25 2.25 21 6.615 21 12Z"/></svg>
                            {{ __('Security') }}
                        </a>
                    @endif
                </div>
            </div>
        </header>

        {{-- At-a-glance stats --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Bookings') }}</p>
                <p class="mt-1 text-2xl font-bold">0</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Enquiries') }}</p>
                <p class="mt-1 text-2xl font-bold">{{ $leads->count() }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('Submissions') }}</p>
                <p class="mt-1 text-2xl font-bold">{{ $submissions->count() }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ __('2FA') }}</p>
                <p class="mt-1 text-sm font-semibold {{ $twoFactorEnabled ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ $twoFactorEnabled ? __('Enabled') : __('Off') }}
                </p>
            </div>
        </div>

        {{-- Main 3-column grid --}}
        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3">
            {{-- Profile summary --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Your profile') }}</h2>
                    <a href="{{ route('profile.edit') }}" wire:navigate class="text-xs font-medium text-hk-primary-600 hover:underline dark:text-hk-primary-400">{{ __('Edit') }}</a>
                </div>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <dt class="text-zinc-500">{{ __('Name') }}</dt>
                        <dd class="max-w-[60%] truncate text-right font-medium">{{ $user->name }}</dd>
                    </div>
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <dt class="text-zinc-500">{{ __('Email') }}</dt>
                        <dd class="max-w-[60%] truncate text-right font-medium">{{ $user->email }}</dd>
                    </div>
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <dt class="text-zinc-500">{{ __('Member since') }}</dt>
                        <dd class="text-right">{{ $user->created_at?->format('M Y') }}</dd>
                    </div>
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <dt class="text-zinc-500">{{ __('2FA') }}</dt>
                        <dd class="text-right">
                            @if ($twoFactorEnabled)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">{{ __('Enabled') }}</span>
                            @elseif (\Illuminate\Support\Facades\Route::has('two-factor.enable'))
                                <a href="{{ route('two-factor.enable') }}" class="text-hk-primary-600 hover:underline dark:text-hk-primary-400">{{ __('Enable') }}</a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="text-hk-primary-600 hover:underline dark:text-hk-primary-400">{{ __('Enable') }}</a>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Bookings --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('My bookings') }}</h2>
                <div class="mt-4 flex flex-col items-start gap-3 rounded-lg border border-dashed border-zinc-200 bg-zinc-50 p-4 text-sm dark:border-zinc-700 dark:bg-zinc-800/50">
                    <div class="flex size-10 items-center justify-center rounded-full bg-white text-hk-primary-600 shadow-sm dark:bg-zinc-900 dark:text-hk-primary-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m0-8.25L3.205 4.91a.75.75 0 0 0-.955.722v12.443a.75.75 0 0 0 .495.704L9 21m0-14.25L15 4.5m0 0 5.795-1.84a.75.75 0 0 1 .955.722v12.443a.75.75 0 0 1-.495.704L15 19.5m0-15v15M9 21l6-1.5"/></svg>
                    </div>
                    <p class="font-medium text-zinc-700 dark:text-zinc-200">{{ __('You have no bookings yet.') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Once you book a tour or activity, it will appear here.') }}</p>
                    @if ($exploreLinks->isNotEmpty())
                        <a href="{{ route($exploreLinks->first()['route']) }}" wire:navigate
                           class="mt-1 inline-flex items-center gap-1 text-sm font-medium text-hk-primary-600 hover:underline dark:text-hk-primary-400">
                            {{ __('Explore trips') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick links --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Quick links') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 text-sm dark:divide-zinc-800">
                    <li>
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center justify-between py-2.5 text-zinc-700 transition hover:text-hk-primary-600 dark:text-zinc-300 dark:hover:text-hk-primary-400">
                            <span>{{ __('Edit profile') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </li>
                    @if (\Illuminate\Support\Facades\Route::has('security.edit'))
                        <li>
                            <a href="{{ route('security.edit') }}" wire:navigate class="flex items-center justify-between py-2.5 text-zinc-700 transition hover:text-hk-primary-600 dark:text-zinc-300 dark:hover:text-hk-primary-400">
                                <span>{{ __('Security & password') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('notifications.edit'))
                        <li>
                            <a href="{{ route('notifications.edit') }}" wire:navigate class="flex items-center justify-between py-2.5 text-zinc-700 transition hover:text-hk-primary-600 dark:text-zinc-300 dark:hover:text-hk-primary-400">
                                <span>{{ __('Notification preferences') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('appearance.edit'))
                        <li>
                            <a href="{{ route('appearance.edit') }}" wire:navigate class="flex items-center justify-between py-2.5 text-zinc-700 transition hover:text-hk-primary-600 dark:text-zinc-300 dark:hover:text-hk-primary-400">
                                <span>{{ __('Appearance') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- My contact submissions --}}
        @if ($submissions->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('My submissions') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($submissions as $s)
                        <li class="py-3 text-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="font-medium">{{ $s->form?->name ?? __('Form') }}</span>
                                    @if ($s->subject)<span class="text-zinc-500"> — {{ $s->subject }}</span>@endif
                                </div>
                                <span class="shrink-0 text-xs text-zinc-500">{{ $s->created_at?->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- My CRM leads (if any) --}}
        @if ($leads->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Your enquiries') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($leads as $l)
                        <li class="py-3 text-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="truncate font-medium">{{ $l->subject ?: $l->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ ucfirst($l->status) }} · {{ $l->stage }}</div>
                                </div>
                                <span class="shrink-0 text-xs text-zinc-500">{{ $l->created_at?->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Recent account activity (audit log) --}}
        @if ($recentActivity->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Recent activity') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($recentActivity as $event)
                        <li class="py-2 text-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="min-w-0 truncate">{{ $event->description ?? $event->event ?? __('Activity') }}</span>
                                <span class="shrink-0 text-xs text-zinc-500">{{ $event->created_at?->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
</div>
