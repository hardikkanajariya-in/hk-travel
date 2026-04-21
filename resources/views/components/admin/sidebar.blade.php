@php
    /**
     * WordPress-style grouped sidebar.
     *
     * The structure defines top-level items and collapsible groups. Groups
     * auto-expand when any of their children match the current route. When the
     * sidebar is collapsed (icon-only) on desktop, groups render their children
     * as a hover flyout instead of inline.
     */
    $iconPaths = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>',
        'paint-brush' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>',
        'cog-6-tooth' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>',
        'squares-2x2' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>',
        'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>',
        'user-plus' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>',
        'envelope' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>',
        'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>',
        'clipboard-document-list' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>',
        'language' => '<path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"/>',
        'link' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>',
        'magnifying-glass' => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>',
        'shield-exclamation' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008ZM12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c-5.385 0-9.75-4.365-9.75-9.75 0-.414 0-.75.75-.75h18c.75 0 .75.336.75.75 0 5.385-4.365 9.75-9.75 9.75Z"/>',
        'view-columns' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/>',
        'queue-list' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>',
        'bars-3' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>',
        'rectangle-group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM2.25 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z"/>',
        'swatch' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z"/>',
        'inbox-arrow-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25 12 5.25m0 0 3 3m-3-3v9M21.75 13.5h-3.86a2.25 2.25 0 0 0-2.012 1.244l-.256.512a2.25 2.25 0 0 1-2.013 1.244h-3.218a2.25 2.25 0 0 1-2.013-1.244l-.256-.512a2.25 2.25 0 0 0-2.013-1.244H2.25m19.5 0V18a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 18v-4.5m19.5 0-2.51-7.533A2.25 2.25 0 0 0 17.09 4.5H6.911a2.25 2.25 0 0 0-2.15 1.588L2.25 13.5"/>',
    ];

    $renderIcon = function (string $name) use ($iconPaths): string {
        $path = $iconPaths[$name] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Z"/>';

        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5 shrink-0">'.$path.'</svg>';
    };

    $groups = [
        ['type' => 'item', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
        [
            'type' => 'group', 'key' => 'content', 'label' => 'Content', 'icon' => 'document',
            'items' => [
                ['label' => 'Pages', 'route' => 'admin.pages'],
                ['label' => 'Menus', 'route' => 'admin.menus'],
                ['label' => 'Footer & sidebar', 'route' => 'admin.widgets'],
                ['label' => 'Forms', 'route' => 'admin.contact-forms'],
                ['label' => 'Submissions', 'route' => 'admin.contact-submissions'],
            ],
        ],
        [
            'type' => 'group', 'key' => 'appearance', 'label' => 'Appearance', 'icon' => 'swatch',
            'items' => [
                ['label' => 'Themes', 'route' => 'admin.themes'],
                ['label' => 'Branding', 'route' => 'admin.branding'],
            ],
        ],
        [
            'type' => 'group', 'key' => 'crm', 'label' => 'CRM', 'icon' => 'user-plus',
            'items' => [
                ['label' => 'Leads', 'route' => 'admin.crm.leads'],
                ['label' => 'Kanban', 'route' => 'admin.crm.kanban'],
                ['label' => 'Pipelines', 'route' => 'admin.crm.pipelines'],
            ],
        ],
        [
            'type' => 'group', 'key' => 'communication', 'label' => 'Communication', 'icon' => 'envelope',
            'items' => [
                ['label' => 'Email templates', 'route' => 'admin.email-templates'],
                ['label' => 'Notifications', 'route' => 'admin.notifications'],
            ],
        ],
        [
            'type' => 'group', 'key' => 'security', 'label' => 'Security', 'icon' => 'shield-check',
            'items' => [
                ['label' => 'Security', 'route' => 'admin.security'],
                ['label' => 'Captcha', 'route' => 'admin.captcha'],
                ['label' => 'Audit log', 'route' => 'admin.audit'],
            ],
        ],
        [
            'type' => 'group', 'key' => 'system', 'label' => 'System', 'icon' => 'cog-6-tooth',
            'items' => [
                ['label' => 'Settings', 'route' => 'admin.settings'],
                ['label' => 'Modules', 'route' => 'admin.modules'],
                ['label' => 'Permalinks', 'route' => 'admin.permalinks'],
                ['label' => 'SEO', 'route' => 'admin.seo'],
            ],
        ],
        ['type' => 'item', 'label' => 'Users', 'route' => 'admin.users', 'icon' => 'users'],
    ];

    $moduleItems = app(\App\Core\Modules\ModuleManager::class)->adminMenuItems();
    if (! empty($moduleItems)) {
        $groups[] = [
            'type' => 'group', 'key' => 'modules', 'label' => 'Modules', 'icon' => 'squares-2x2',
            'items' => $moduleItems,
        ];
    }

    $isRouteActive = fn (string $routeName): bool => request()->routeIs($routeName) || request()->routeIs($routeName.'.*');

    $isGroupActive = function (array $group) use ($isRouteActive): bool {
        foreach ($group['items'] ?? [] as $item) {
            if ($isRouteActive($item['route'])) {
                return true;
            }
        }

        return false;
    };

    $href = fn (string $route): string => \Illuminate\Support\Facades\Route::has($route) ? route($route) : '#';
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 md:hidden"
    style="display: none;"
    aria-hidden="true"
></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col overflow-x-hidden border-r border-zinc-200 bg-white transition-all duration-200 ease-in-out md:static md:z-auto md:translate-x-0 dark:border-zinc-800 dark:bg-zinc-900"
    :class="{
        '-translate-x-full': ! sidebarOpen,
        'translate-x-0': sidebarOpen,
        'md:w-64': ! sidebarCollapsed,
        'md:w-16': sidebarCollapsed,
    }"
    aria-label="{{ __('Admin navigation') }}"
>
    {{-- Brand --}}
    <div class="flex h-16 items-center gap-3 border-b border-zinc-200 px-4 dark:border-zinc-800">
        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-hk-primary-600 font-bold text-white">
            {{ substr(config('hk.brand.name', 'HK'), 0, 1) }}
        </div>
        <span class="truncate font-semibold" x-show="! sidebarCollapsed" x-transition.opacity>
            {{ config('hk.brand.name') }}
        </span>
        <button
            type="button"
            class="ml-auto rounded-md p-1.5 text-zinc-500 hover:bg-zinc-100 md:hidden dark:hover:bg-zinc-800"
            @click="sidebarOpen = false"
            aria-label="{{ __('Close menu') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-0.5 overflow-y-auto p-3">
        @foreach ($groups as $group)
            @if ($group['type'] === 'item')
                @php $active = $isRouteActive($group['route']); @endphp
                <a
                    href="{{ $href($group['route']) }}"
                    wire:navigate
                    @class([
                        'group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition',
                        'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300' => $active,
                        'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => ! $active,
                    ])
                    :title="sidebarCollapsed ? @js($group['label']) : null"
                >
                    {!! $renderIcon($group['icon']) !!}
                    <span class="truncate" x-show="! sidebarCollapsed" x-transition.opacity>{{ $group['label'] }}</span>
                </a>
            @else
                @php $groupActive = $isGroupActive($group); @endphp
                <div
                    x-data="{ open: @js($groupActive) }"
                    x-effect="if (sidebarCollapsed) open = false"
                    class="group relative"
                >
                    <button
                        type="button"
                        @click="if (sidebarCollapsed) { sidebarCollapsed = false; $nextTick(() => open = true) } else { open = ! open }"
                        @class([
                            'flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition',
                            'bg-hk-primary-50 text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300' => $groupActive,
                            'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => ! $groupActive,
                        ])
                        :aria-expanded="open.toString()"
                        :title="sidebarCollapsed ? @js($group['label']) : null"
                    >
                        {!! $renderIcon($group['icon']) !!}
                        <span class="flex-1 truncate text-left" x-show="! sidebarCollapsed" x-transition.opacity>{{ $group['label'] }}</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="size-4 shrink-0 transition-transform duration-200"
                            :class="{ 'rotate-90': open }"
                            x-show="! sidebarCollapsed"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>

                    {{-- Inline children (expanded sidebar) --}}
                    <div
                        x-show="open && ! sidebarCollapsed"
                        x-collapse
                        class="mt-0.5 space-y-0.5"
                        style="display: none;"
                    >
                        @foreach ($group['items'] as $item)
                            @php $active = $isRouteActive($item['route']); @endphp
                            <a
                                href="{{ $href($item['route']) }}"
                                wire:navigate
                                @class([
                                    'flex items-center rounded-md py-1.5 pl-11 pr-3 text-sm transition',
                                    'bg-hk-primary-50 font-medium text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300' => $active,
                                    'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! $active,
                                ])
                            >
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Flyout (collapsed sidebar, desktop only) --}}
                    <div
                        x-show="sidebarCollapsed"
                        x-cloak
                        class="pointer-events-none absolute left-full top-0 z-50 ml-2 min-w-48 rounded-md border border-zinc-200 bg-white p-1 opacity-0 shadow-lg transition group-hover:pointer-events-auto group-hover:opacity-100 dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        <div class="mb-1 border-b border-zinc-100 px-2 py-1 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:border-zinc-800">
                            {{ $group['label'] }}
                        </div>
                        @foreach ($group['items'] as $item)
                            @php $active = $isRouteActive($item['route']); @endphp
                            <a
                                href="{{ $href($item['route']) }}"
                                wire:navigate
                                @class([
                                    'block rounded px-2 py-1.5 text-sm',
                                    'bg-hk-primary-50 font-medium text-hk-primary-700 dark:bg-hk-primary-950 dark:text-hk-primary-300' => $active,
                                    'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' => ! $active,
                                ])
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- Footer / collapse toggle --}}
    <div class="shrink-0 border-t border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center justify-between px-3 py-2 text-xs text-zinc-500">
            <span x-show="! sidebarCollapsed" x-transition.opacity>v0.1.0</span>
            <button
                type="button"
                class="ml-auto hidden rounded-md p-1.5 text-zinc-500 hover:bg-zinc-100 md:inline-flex dark:hover:bg-zinc-800"
                @click="sidebarCollapsed = ! sidebarCollapsed; localStorage.setItem('hk.admin.sidebarCollapsed', sidebarCollapsed ? '1' : '0')"
                :aria-label="sidebarCollapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"
                    class="size-4 transition-transform"
                    :class="{ 'rotate-180': sidebarCollapsed }"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                </svg>
            </button>
        </div>

        {{-- Credit --}}
        <div
            class="border-t border-zinc-200/70 px-3 py-2 text-[11px] text-zinc-400 dark:border-zinc-800/70"
            x-show="! sidebarCollapsed"
            x-transition.opacity
        >
            Powered by
            <a
                href="https://hardikkanajariya.in"
                target="_blank"
                rel="noopener"
                class="font-medium text-zinc-500 hover:text-hk-primary-600 dark:text-zinc-300"
            >hardikkanajariya.in</a>
        </div>

        {{-- Collapsed credit: just a heart/initial --}}
        <div
            class="flex items-center justify-center border-t border-zinc-200/70 py-2 text-[11px] text-zinc-400 dark:border-zinc-800/70"
            x-show="sidebarCollapsed"
            x-cloak
        >
            <a
                href="https://hardikkanajariya.in"
                target="_blank"
                rel="noopener"
                title="Powered by hardikkanajariya.in"
                class="hover:text-hk-primary-600"
            >HK</a>
        </div>
    </div>
</aside>
