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
    @endphp

    <x-theme.breadcrumbs />

    <section class="mx-auto max-w-6xl space-y-8 px-4 py-10 sm:px-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ __('Welcome back, :name', ['name' => $user->name]) }}</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Manage your account, requests, and recent activity.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.edit') }}" wire:navigate class="rounded-md border border-zinc-300 px-3 py-2 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">{{ __('Edit profile') }}</a>
                @if (\Illuminate\Support\Facades\Route::has('security.edit'))
                    <a href="{{ route('security.edit') }}" wire:navigate class="rounded-md border border-zinc-300 px-3 py-2 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">{{ __('Security') }}</a>
                @endif
            </div>
        </header>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Profile summary --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Your profile') }}</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-zinc-500">{{ __('Name') }}</dt><dd class="font-medium">{{ $user->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">{{ __('Email') }}</dt><dd class="font-medium">{{ $user->email }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">{{ __('Member since') }}</dt><dd>{{ $user->created_at?->format('M Y') }}</dd></div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500">{{ __('2FA') }}</dt>
                        <dd>
                            @if ($user->two_factor_secret ?? null)
                                <span class="text-green-600">{{ __('Enabled') }}</span>
                            @elseif (\Illuminate\Support\Facades\Route::has('two-factor.enable'))
                                <a href="{{ route('two-factor.enable') }}" class="text-hk-primary-600 hover:underline">{{ __('Enable') }}</a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="text-hk-primary-600 hover:underline">{{ __('Enable') }}</a>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Bookings placeholder (hooked into step 45) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('My bookings') }}</h2>
                <p class="mt-4 text-sm text-zinc-500">{{ __('You have no bookings yet.') }}</p>
                <p class="mt-2 text-xs text-zinc-400">{{ __('Once you book a tour or activity, it will appear here.') }}</p>
            </div>

            {{-- Quick links --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Quick links') }}</h2>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('profile.edit') }}" wire:navigate class="text-hk-primary-600 hover:underline">→ {{ __('Edit profile') }}</a></li>
                    @if (\Illuminate\Support\Facades\Route::has('security.edit'))
                        <li><a href="{{ route('security.edit') }}" wire:navigate class="text-hk-primary-600 hover:underline">→ {{ __('Security & password') }}</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('notifications.edit'))
                        <li><a href="{{ route('notifications.edit') }}" wire:navigate class="text-hk-primary-600 hover:underline">→ {{ __('Notification preferences') }}</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('appearance.edit'))
                        <li><a href="{{ route('appearance.edit') }}" wire:navigate class="text-hk-primary-600 hover:underline">→ {{ __('Appearance') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- My contact submissions --}}
        @if ($submissions->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('My submissions') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($submissions as $s)
                        <li class="py-3 text-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium">{{ $s->form?->name ?? __('Form') }}</span>
                                    @if ($s->subject)<span class="text-zinc-500"> — {{ $s->subject }}</span>@endif
                                </div>
                                <span class="text-xs text-zinc-500">{{ $s->created_at?->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- My CRM leads (if any) --}}
        @if ($leads->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Your enquiries') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($leads as $l)
                        <li class="py-3 text-sm flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $l->subject ?: $l->name }}</div>
                                <div class="text-xs text-zinc-500">{{ ucfirst($l->status) }} · {{ $l->stage }}</div>
                            </div>
                            <span class="text-xs text-zinc-500">{{ $l->created_at?->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Recent account activity (audit log) --}}
        @if ($recentActivity->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">{{ __('Recent activity') }}</h2>
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($recentActivity as $event)
                        <li class="py-2 text-sm flex items-center justify-between">
                            <span>{{ $event->description ?? $event->event ?? __('Activity') }}</span>
                            <span class="text-xs text-zinc-500">{{ $event->created_at?->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
</div>
