<?php

use App\Core\Installer\InstallationState;
use App\Core\Modules\ModuleManager;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('HK Travel — Install')] #[Layout('components.layouts.installer')] class extends Component {
    public int $step = 1;

    public string $appName = 'HK Travel';
    public string $appUrl = '';
    public string $locale = 'en';
    public string $timezone = 'UTC';

    public string $dbConnection = 'sqlite';
    public string $dbHost = '127.0.0.1';
    public string $dbPort = '3306';
    public string $dbDatabase = '';
    public string $dbUsername = 'root';
    public string $dbPassword = '';

    public string $adminName = '';
    public string $adminEmail = '';
    public string $adminPassword = '';
    public string $adminPasswordConfirmation = '';

    /** @var array<string, bool> */
    public array $modules = [];

    public ?string $error = null;
    public bool $installing = false;

    public function mount(): void
    {
        $this->appUrl = (string) config('app.url');
        $this->timezone = (string) config('app.timezone', 'UTC');

        foreach (config('hk-modules.modules', []) as $key => $module) {
            $this->modules[$key] = $module['enabled'] ?? false;
        }
    }

    #[Computed]
    public function totalSteps(): int
    {
        return 5;
    }

    #[Computed]
    public function requirements(): array
    {
        return [
            ['label' => 'PHP >= 8.4', 'ok' => version_compare(PHP_VERSION, '8.4.0', '>=')],
            ['label' => 'OpenSSL extension', 'ok' => extension_loaded('openssl')],
            ['label' => 'Mbstring extension', 'ok' => extension_loaded('mbstring')],
            ['label' => 'Tokenizer extension', 'ok' => extension_loaded('tokenizer')],
            ['label' => 'JSON extension', 'ok' => extension_loaded('json')],
            ['label' => 'PDO extension', 'ok' => extension_loaded('pdo')],
            ['label' => 'Ctype extension', 'ok' => extension_loaded('ctype')],
            ['label' => 'Fileinfo extension', 'ok' => extension_loaded('fileinfo')],
            ['label' => 'storage/ writable', 'ok' => is_writable(storage_path())],
            ['label' => 'bootstrap/cache writable', 'ok' => is_writable(base_path('bootstrap/cache'))],
            ['label' => '.env writable', 'ok' => is_writable(base_path('.env'))],
        ];
    }

    #[Computed]
    public function moduleList(): array
    {
        return config('hk-modules.modules', []);
    }

    public function next(): void
    {
        $this->error = null;
        $this->validateCurrentStep();
        $this->step = min($this->step + 1, $this->totalSteps());
    }

    public function back(): void
    {
        $this->error = null;
        $this->step = max($this->step - 1, 1);
    }

    protected function validateCurrentStep(): void
    {
        match ($this->step) {
            1 => $this->validateRequirements(),
            2 => $this->validate([
                'appName' => 'required|string|max:120',
                'appUrl' => 'required|url',
                'locale' => 'required|string|max:8',
                'timezone' => 'required|string|max:64',
            ]),
            3 => $this->validateDatabase(),
            4 => $this->validate([
                'adminName' => 'required|string|max:120',
                'adminEmail' => 'required|email|max:255',
                'adminPassword' => 'required|string|min:8|confirmed',
            ]),
            default => null,
        };
    }

    protected function validateRequirements(): void
    {
        foreach ($this->requirements as $req) {
            if (! $req['ok']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'requirements' => "Missing: {$req['label']}",
                ]);
            }
        }
    }

    protected function validateDatabase(): void
    {
        $rules = ['dbConnection' => 'required|in:sqlite,mysql,pgsql,mariadb'];

        if ($this->dbConnection !== 'sqlite') {
            $rules += [
                'dbHost' => 'required|string',
                'dbPort' => 'required|string',
                'dbDatabase' => 'required|string',
                'dbUsername' => 'required|string',
            ];
        }

        $this->validate($rules);
    }

    public function install(InstallationState $state): mixed
    {
        $this->error = null;
        $this->validateCurrentStep();
        $this->installing = true;

        try {
            $this->writeEnv();
            Artisan::call('config:clear');

            Artisan::call('migrate', ['--force' => true, '--seed' => true]);

            User::query()->create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => Hash::make($this->adminPassword),
                'email_verified_at' => now(),
            ])->assignRole('super-admin');

            $this->persistEnabledModules();

            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            $state->markInstalled();

            return $this->redirect('/login', navigate: false);
        } catch (\Throwable $e) {
            $this->installing = false;
            $this->error = $e->getMessage();

            return null;
        }
    }

    protected function writeEnv(): void
    {
        $pairs = [
            'APP_NAME='.$this->appName,
            'APP_URL='.rtrim($this->appUrl, '/'),
            'APP_LOCALE='.$this->locale,
            'APP_TIMEZONE='.$this->timezone,
            'DB_CONNECTION='.$this->dbConnection,
        ];

        if ($this->dbConnection === 'sqlite') {
            $sqlitePath = database_path('database.sqlite');
            if (! file_exists($sqlitePath)) {
                touch($sqlitePath);
            }
            $pairs[] = 'DB_DATABASE='.$sqlitePath;
        } else {
            $pairs[] = 'DB_HOST='.$this->dbHost;
            $pairs[] = 'DB_PORT='.$this->dbPort;
            $pairs[] = 'DB_DATABASE='.$this->dbDatabase;
            $pairs[] = 'DB_USERNAME='.$this->dbUsername;
            $pairs[] = 'DB_PASSWORD='.$this->dbPassword;
        }

        Artisan::call('hk:env:set', ['pairs' => $pairs]);
    }

    /**
     * Persist module enable/disable choices by writing to the modules
     * settings table later. For v1 we just keep them in the lock file
     * since admin module-toggle UI is built next.
     */
    protected function persistEnabledModules(): void
    {
        $enabled = collect($this->modules)->filter()->keys()->all();
        cache()->forever('hk:installer:enabled-modules', $enabled);
    }
};

?>

<div class="mx-auto w-full max-w-3xl">
    <div class="mb-8 text-center">
        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/70 dark:bg-zinc-900/60 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-hk-primary-700 dark:text-hk-primary-300 ring-1 ring-hk-primary-200/60 dark:ring-hk-primary-800/60 shadow-sm backdrop-blur">
            <span class="size-1.5 rounded-full bg-hk-primary-500 animate-pulse"></span>
            Setup wizard
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
            <span class="hk-gradient-text">HK Travel</span>
            <span class="text-zinc-900 dark:text-zinc-100">— Setup</span>
        </h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
            Step {{ $step }} of {{ $this->totalSteps }} — let's get your site online.
        </p>
    </div>

    <ol class="mb-8 hidden sm:flex items-center justify-between gap-2" aria-label="Progress">
        @foreach ([1 => 'Server', 2 => 'App', 3 => 'Database', 4 => 'Admin', 5 => 'Modules'] as $i => $label)
            <li class="flex flex-1 items-center gap-2">
                <span @class([
                    'flex size-8 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-all duration-300',
                    'hk-gradient-primary text-white shadow-md shadow-hk-primary-500/40 scale-110' => $step === $i,
                    'bg-hk-primary-100 text-hk-primary-700 dark:bg-hk-primary-900 dark:text-hk-primary-200' => $step > $i,
                    'bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500' => $step < $i,
                ])>
                    @if ($step > $i)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @else
                        {{ $i }}
                    @endif
                </span>
                <span @class([
                    'text-xs font-medium transition-colors',
                    'text-zinc-900 dark:text-zinc-100' => $step >= $i,
                    'text-zinc-400 dark:text-zinc-600' => $step < $i,
                ])>{{ $label }}</span>
                @if ($i < 5)
                    <span @class([
                        'h-px flex-1 transition-colors',
                        'bg-hk-primary-400' => $step > $i,
                        'bg-zinc-200 dark:bg-zinc-800' => $step <= $i,
                    ])></span>
                @endif
            </li>
        @endforeach
    </ol>

    <div class="mb-8 sm:hidden h-2 w-full overflow-hidden rounded-full bg-zinc-200/70 dark:bg-zinc-800/70 shadow-inner">
        <div class="h-2 rounded-full hk-gradient-primary shadow-[0_0_12px_rgb(59_130_246/0.5)] transition-all duration-500 ease-out"
             style="width: {{ ($step / $this->totalSteps) * 100 }}%"></div>
    </div>

    @if ($error)
        <x-ui.alert variant="danger" :dismissible="true" class="mb-6">
            {{ $error }}
        </x-ui.alert>
    @endif

    <x-ui.card>
        @if ($step === 1)
            <h2 class="mb-4 text-lg font-medium">Server requirements</h2>
            <ul class="space-y-2 text-sm">
                @foreach ($this->requirements as $req)
                    <li class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 py-2 last:border-0">
                        <span>{{ $req['label'] }}</span>
                        @if ($req['ok'])
                            <x-ui.badge variant="success">OK</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger">Missing</x-ui.badge>
                        @endif
                    </li>
                @endforeach
            </ul>
            @error('requirements')<p class="mt-3 text-sm text-hk-danger">{{ $message }}</p>@enderror
        @endif

        @if ($step === 2)
            <h2 class="mb-4 text-lg font-medium">Application settings</h2>
            <div class="space-y-4">
                <x-ui.input wire:model="appName" label="Site name" required />
                <x-ui.input wire:model="appUrl" label="Site URL" type="url" required hint="e.g. https://example.com" />
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input wire:model="locale" label="Default locale" required />
                    <x-ui.input wire:model="timezone" label="Timezone" required />
                </div>
            </div>
        @endif

        @if ($step === 3)
            <h2 class="mb-4 text-lg font-medium">Database connection</h2>
            <div class="space-y-4">
                <label class="block">
                    <span class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Driver</span>
                    <select wire:model.live="dbConnection"
                            class="block w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                        <option value="sqlite">SQLite (recommended for small deployments)</option>
                        <option value="mysql">MySQL</option>
                        <option value="mariadb">MariaDB</option>
                        <option value="pgsql">PostgreSQL</option>
                    </select>
                </label>

                @if ($dbConnection !== 'sqlite')
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input wire:model="dbHost" label="Host" required />
                        <x-ui.input wire:model="dbPort" label="Port" required />
                    </div>
                    <x-ui.input wire:model="dbDatabase" label="Database name" required />
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input wire:model="dbUsername" label="Username" required />
                        <x-ui.input wire:model="dbPassword" type="password" label="Password" />
                    </div>
                @else
                    <p class="rounded-md bg-zinc-50 dark:bg-zinc-900 p-3 text-sm text-zinc-600 dark:text-zinc-400">
                        SQLite database will be created at <code class="font-mono text-xs">database/database.sqlite</code>.
                    </p>
                @endif
            </div>
        @endif

        @if ($step === 4)
            <h2 class="mb-4 text-lg font-medium">Administrator account</h2>
            <div class="space-y-4">
                <x-ui.input wire:model="adminName" label="Full name" required />
                <x-ui.input wire:model="adminEmail" type="email" label="Email" required />
                <x-ui.input wire:model="adminPassword" type="password" label="Password" required hint="At least 8 characters" />
                <x-ui.input wire:model="adminPasswordConfirmation" type="password" label="Confirm password" required />
            </div>
        @endif

        @if ($step === 5)
            <h2 class="mb-4 text-lg font-medium">Enable travel modules</h2>
            <p class="mb-4 text-sm text-zinc-600 dark:text-zinc-400">
                Pick the features to activate now. You can toggle these any time from the admin panel.
            </p>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($this->moduleList as $key => $module)
                    <label class="flex items-start gap-3 rounded-md border border-zinc-200 dark:border-zinc-800 p-3 cursor-pointer hover:border-hk-primary-400 transition">
                        <input type="checkbox"
                               wire:model="modules.{{ $key }}"
                               class="mt-0.5 size-4 rounded border-zinc-300 text-hk-primary-600 focus:ring-hk-primary-500">
                        <span>
                            <span class="block text-sm font-medium">{{ $module['label'] ?? $key }}</span>
                            <span class="block text-xs text-zinc-500">{{ $key }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        @endif

        <div class="mt-8 flex items-center justify-between">
            @if ($step > 1)
                <x-ui.button variant="ghost" wire:click="back" wire:loading.attr="disabled">Back</x-ui.button>
            @else
                <span></span>
            @endif

            @if ($step < $this->totalSteps)
                <x-ui.button wire:click="next" wire:loading.attr="disabled">Continue</x-ui.button>
            @else
                <x-ui.button wire:click="install" :loading="$installing" wire:loading.attr="disabled">
                    Install
                </x-ui.button>
            @endif
        </div>
    </x-ui.card>
</div>
