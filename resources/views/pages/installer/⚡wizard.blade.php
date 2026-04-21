<?php

use App\Core\Installer\InstallationState;
use App\Core\Modules\ModuleManager;
use App\Core\Settings\SettingsRepository;
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
    public string $timezone = 'Asia/Kolkata';

    public string $dbConnection = 'sqlite';
    public string $dbHost = '127.0.0.1';
    public string $dbPort = '3306';
    public string $dbDatabase = 'hk-travel';
    public string $dbUsername = 'root';
    public string $dbPassword = '';

    public string $adminName = 'Hardik Kanajariya';
    public string $adminEmail = 'hardik@hardikkanajariya.in';
    public string $adminPassword = 'Nud@#38648';
    public string $adminPasswordConfirmation = 'Nud@#38648';

    /** @var array<string, bool> */
    public array $modules = [];

    public ?string $error = null;
    public bool $installing = false;
    public bool $installComplete = false;

    /**
     * Real install progress. Each entry:
     *   key       => stable id (matches translation key)
     *   label     => translated label
     *   status    => 'pending' | 'running' | 'done' | 'failed' | 'skipped'
     *   message   => optional informational/error message
     *   duration  => seconds elapsed (float, after completion)
     *   detail    => optional structured info (e.g. counts)
     *
     * @var array<int, array<string, mixed>>
     */
    public array $progressSteps = [];

    public int $currentStepIndex = 0;

    public function mount(): void
    {
        $this->appUrl = (string) config('app.url');
        $envTimezone = (string) config('app.timezone', 'UTC');
        if ($envTimezone && $envTimezone !== 'UTC') {
            $this->timezone = $envTimezone;
        }

        foreach (config('hk-modules.modules', []) as $key => $module) {
            // Default to enabled so a fresh install ships with everything
            // turned on. Users can untick on the modules step.
            $this->modules[$key] = true;
        }
    }

    /**
     * Toggle every module on/off in one click. If any module is currently
     * disabled we enable everything; otherwise we disable everything.
     */
    public function toggleAllModules(): void
    {
        $allOn = collect($this->modules)->every(fn ($v) => (bool) $v);
        $target = ! $allOn;

        foreach ($this->modules as $key => $_) {
            $this->modules[$key] = $target;
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

    /**
     * All IANA timezone identifiers, formatted for display in the select.
     * Returns a [identifier => "Region/City (UTC±hh:mm)"] map so the user
     * can scan offsets quickly.
     */
    #[Computed]
    public function timezones(): array
    {
        $now = new \DateTimeImmutable('now');
        $list = [];

        foreach (\DateTimeZone::listIdentifiers() as $identifier) {
            try {
                $offsetSeconds = (new \DateTimeZone($identifier))->getOffset($now);
            } catch (\Throwable) {
                continue;
            }

            $sign = $offsetSeconds >= 0 ? '+' : '-';
            $abs = abs($offsetSeconds);
            $hours = intdiv($abs, 3600);
            $minutes = intdiv($abs % 3600, 60);
            $offset = sprintf('UTC%s%02d:%02d', $sign, $hours, $minutes);

            $list[$identifier] = $identifier.' ('.$offset.')';
        }

        return $list;
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
                'timezone' => 'required|string|max:128|timezone',
            ]),
            3 => $this->validateDatabase(),
            4 => $this->validate([
                'adminName' => 'required|string|max:120',
                'adminEmail' => 'required|email|max:255',
                'adminPassword' => 'required|string|min:8|confirmed:adminPasswordConfirmation',
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

    /**
     * Kick off the install. Validates the final step, builds the real
     * progress checklist and asks the front-end to start running steps
     * one by one via runStep(). Each step is a real Livewire round-trip
     * so the UI reflects exactly what's happening server-side.
     */
    public function startInstall(): void
    {
        $this->error = null;
        $this->validateCurrentStep();

        // Long-running install (migrate:fresh + seed for all modules) can
        // easily exceed PHP's default max_execution_time. Lift the cap and
        // keep going even if the user closes the tab.
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        $this->installing = true;
        $this->installComplete = false;
        $this->currentStepIndex = 0;
        $this->progressSteps = collect([
            'env', 'migrate', 'seed', 'locale', 'admin', 'modules', 'cache', 'finalize',
        ])->map(fn (string $key) => [
            'key' => $key,
            'label' => __('installer.progress.steps.'.$key),
            'status' => 'pending',
            'message' => null,
            'duration' => null,
            'detail' => null,
        ])->all();

        $this->dispatch('hk-install-started');
    }

    /**
     * Execute the next pending step and report its real outcome back to
     * the browser. The frontend chains calls to this method until the
     * returned payload indicates `done` or `failed`.
     *
     * @return array{done: bool, failed?: bool, redirect?: string, key?: string, message?: ?string}
     */
    public function runStep(InstallationState $state): array
    {
        if (! $this->installing || $this->currentStepIndex >= count($this->progressSteps)) {
            return ['done' => true];
        }

        @set_time_limit(0);

        $index = $this->currentStepIndex;
        $key = $this->progressSteps[$index]['key'];

        $this->progressSteps[$index]['status'] = 'running';
        $this->progressSteps[$index]['message'] = null;
        $startedAt = microtime(true);

        try {
            $detail = match ($key) {
                'env' => $this->stepWriteEnv(),
                'migrate' => $this->stepMigrate(),
                'seed' => $this->stepSeed(),
                'locale' => $this->stepLocale(),
                'admin' => $this->stepCreateAdmin(),
                'modules' => $this->stepModules(),
                'cache' => $this->stepClearCaches(),
                'finalize' => $this->stepFinalize($state),
                default => [],
            };

            $this->progressSteps[$index]['status'] = 'done';
            $this->progressSteps[$index]['duration'] = round(microtime(true) - $startedAt, 2);
            $this->progressSteps[$index]['message'] = $detail['message'] ?? null;
            $this->progressSteps[$index]['detail'] = $detail;

            $this->currentStepIndex++;

            if ($this->currentStepIndex >= count($this->progressSteps)) {
                $this->installComplete = true;
                $this->installing = false;

                return ['done' => true, 'redirect' => url('/login'), 'key' => $key];
            }

            return ['done' => false, 'key' => $key];
        } catch (\Throwable $e) {
            $this->progressSteps[$index]['status'] = 'failed';
            $this->progressSteps[$index]['duration'] = round(microtime(true) - $startedAt, 2);
            $this->progressSteps[$index]['message'] = $e->getMessage();
            $this->installing = false;
            $this->error = $e->getMessage();

            report($e);

            return ['done' => true, 'failed' => true, 'key' => $key, 'message' => $e->getMessage()];
        }
    }

    /** Reset progress so the user can retry after fixing a failure. */
    public function resetInstallProgress(): void
    {
        $this->installing = false;
        $this->installComplete = false;
        $this->currentStepIndex = 0;
        $this->progressSteps = [];
        $this->error = null;
    }

    protected function stepWriteEnv(): array
    {
        $this->writeEnv();
        Artisan::call('config:clear');

        // config:clear only removes the compiled cache file — the running
        // process still has the old values in memory. Re-read the .env file
        // and push the new DB settings into the live config + reconnect, so
        // stepMigrate() uses the connection the user just entered instead of
        // whatever was configured when the page first loaded.
        $this->refreshRuntimeConfig();

        return ['message' => __('installer.progress.detail.env', ['driver' => $this->dbConnection])];
    }

    /**
     * Re-read the written .env values and apply them to the in-memory config
     * and DB manager so subsequent steps don't use stale connections.
     */
    protected function refreshRuntimeConfig(): void
    {
        // Reload .env into $_ENV / putenv so config() reads fresh values.
        if (file_exists(base_path('.env'))) {
            $dotenv = \Dotenv\Dotenv::createMutable(base_path());
            try {
                $dotenv->load();
            } catch (\Throwable) {
                // Immutable env — override manually below instead.
            }
        }

        // Push the user-chosen values directly into the running config.
        config([
            'database.default' => $this->dbConnection,
            'database.connections.'.$this->dbConnection.'.host' => $this->dbHost,
            'database.connections.'.$this->dbConnection.'.port' => $this->dbPort,
            'database.connections.'.$this->dbConnection.'.database' => $this->dbConnection === 'sqlite'
                ? database_path('database.sqlite')
                : $this->dbDatabase,
            'database.connections.'.$this->dbConnection.'.username' => $this->dbUsername,
            'database.connections.'.$this->dbConnection.'.password' => $this->dbPassword,
        ]);

        // Purge all cached connections so the next DB call opens a fresh one
        // using the values we just set.
        \Illuminate\Support\Facades\DB::purge($this->dbConnection);
        \Illuminate\Support\Facades\DB::reconnect($this->dbConnection);
    }

    protected function stepMigrate(): array
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        $output = trim(Artisan::output());

        // Migration runner prints one "DONE" per migration applied.
        $migrationCount = substr_count($output, 'DONE');

        return [
            'message' => __('installer.progress.detail.migrate', ['count' => $migrationCount]),
            'migrations' => $migrationCount,
        ];
    }

    protected function stepSeed(): array
    {
        Artisan::call('db:seed', ['--force' => true]);

        return ['message' => __('installer.progress.detail.seed')];
    }

    protected function stepLocale(): array
    {
        // Promote the locale chosen during install to "default" and ensure
        // it's active. The seeder activates en/hi/gu by default; this just
        // honours the user's pick from step 2 even if they chose a code
        // outside that trio.
        \App\Models\Language::query()->update(['is_default' => false]);
        $updated = \App\Models\Language::where('code', $this->locale)
            ->update(['is_default' => true, 'is_active' => true]);

        if ($updated === 0) {
            return ['message' => __('installer.progress.detail.locale_missing', ['code' => $this->locale])];
        }

        return ['message' => __('installer.progress.detail.locale', ['code' => $this->locale])];
    }

    protected function stepCreateAdmin(): array
    {
        User::query()->create([
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'password' => Hash::make($this->adminPassword),
            'email_verified_at' => now(),
        ])->assignRole('super-admin');

        return ['message' => __('installer.progress.detail.admin', ['email' => $this->adminEmail])];
    }

    protected function stepModules(): array
    {
        $this->persistEnabledModules();
        $count = collect($this->modules)->filter()->count();

        return [
            'message' => __('installer.progress.detail.modules', ['count' => $count]),
            'count' => $count,
        ];
    }

    protected function stepClearCaches(): array
    {
        Artisan::call('config:clear');
        // view:clear is intentionally skipped here: clearing compiled Blade
        // files mid-session would delete the compiled wizard view itself,
        // causing a FileNotFoundException on the very next Livewire round-trip.
        // Views recompile automatically on first page load after the redirect.
        Artisan::call('cache:clear');

        return ['message' => __('installer.progress.detail.cache')];
    }

    protected function stepFinalize(InstallationState $state): array
    {
        $state->markInstalled();

        return ['message' => __('installer.progress.detail.finalize')];
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
     * Persist module enable/disable choices to the settings store so the
     * admin Modules page (and ModuleManager) see the same values the user
     * picked during install. Also keeps a cache copy for legacy lookups.
     */
    protected function persistEnabledModules(): void
    {
        $settings = app(SettingsRepository::class);

        foreach (array_keys(config('hk-modules.modules', [])) as $key) {
            $settings->set("modules.$key.enabled", (bool) ($this->modules[$key] ?? false));
        }

        $settings->flush();

        $enabled = collect($this->modules)->filter()->keys()->all();
        cache()->forever('hk:installer:enabled-modules', $enabled);
    }
};

?>

<div class="mx-auto w-full max-w-3xl">
    <div class="mb-8 text-center">
        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/70 dark:bg-zinc-900/60 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-hk-primary-700 dark:text-hk-primary-300 ring-1 ring-hk-primary-200/60 dark:ring-hk-primary-800/60 shadow-sm backdrop-blur">
            <span class="size-1.5 rounded-full bg-hk-primary-500 animate-pulse"></span>
            {{ __('installer.wizard.badge') }}
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
            <span class="hk-gradient-text">{{ __('installer.wizard.title') }}</span>
        </h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('installer.wizard.progress', ['current' => $step, 'total' => $this->totalSteps]) }}
        </p>
    </div>

    <ol class="mb-8 hidden sm:flex items-center justify-between gap-2" aria-label="{{ __('installer.wizard.progress_aria') }}">
        @foreach ([1 => __('installer.steps.server'), 2 => __('installer.steps.app'), 3 => __('installer.steps.database'), 4 => __('installer.steps.admin'), 5 => __('installer.steps.modules')] as $i => $label)
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
            <h2 class="mb-1 text-lg font-semibold">{{ __('installer.server.heading') }}</h2>
            <p class="mb-5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('installer.server.subtitle') }}
            </p>
            <ul class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                @foreach ($this->requirements as $req)
                    <li @class([
                        'flex items-center gap-3 rounded-xl border p-3 transition-colors',
                        'border-emerald-200/70 bg-emerald-50/60 dark:border-emerald-800/60 dark:bg-emerald-950/30' => $req['ok'],
                        'border-red-200/70 bg-red-50/60 dark:border-red-800/60 dark:bg-red-950/30' => ! $req['ok'],
                    ])>
                        <span @class([
                            'flex size-7 shrink-0 items-center justify-center rounded-full ring-1 ring-inset',
                            'bg-emerald-500 text-white ring-emerald-600/30 shadow-sm shadow-emerald-500/30' => $req['ok'],
                            'bg-red-500 text-white ring-red-600/30 shadow-sm shadow-red-500/30' => ! $req['ok'],
                        ])>
                            @if ($req['ok'])
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </span>
                        <span class="grow font-medium text-zinc-800 dark:text-zinc-100">{{ $req['label'] }}</span>
                        <span @class([
                            'text-[11px] font-semibold uppercase tracking-wider',
                            'text-emerald-700 dark:text-emerald-300' => $req['ok'],
                            'text-red-700 dark:text-red-300' => ! $req['ok'],
                        ])>
                            {{ $req['ok'] ? __('installer.server.ok') : __('installer.server.missing') }}
                        </span>
                    </li>
                @endforeach
            </ul>
            @error('requirements')<p class="mt-3 text-sm text-hk-danger">{{ $message }}</p>@enderror
        @endif

        @if ($step === 2)
            <h2 class="mb-4 text-lg font-medium">{{ __('installer.app.heading') }}</h2>
            <div class="space-y-4">
                <x-ui.input wire:model="appName" :label="__('installer.app.fields.site_name')" required />
                <x-ui.input wire:model="appUrl" :label="__('installer.app.fields.site_url')" type="url" required :hint="__('installer.app.fields.site_url_hint')" />
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.select
                        wire:model="locale"
                        :label="__('installer.app.fields.locale')"
                        required
                        :options="[
                            'en' => __('installer.app.locales.en'),
                            'hi' => __('installer.app.locales.hi'),
                            'gu' => __('installer.app.locales.gu'),
                        ]"
                    />
                    <x-ui.select
                        wire:model="timezone"
                        :value="$timezone"
                        :label="__('installer.app.fields.timezone')"
                        required
                        searchable
                        :options="$this->timezones"
                    />
                </div>
            </div>
        @endif

        @if ($step === 3)
            <h2 class="mb-4 text-lg font-medium">{{ __('installer.database.heading') }}</h2>
            <div class="space-y-4">
                <x-ui.select
                    wire:model.live="dbConnection"
                    :label="__('installer.database.driver')"
                    :options="[
                        'sqlite' => __('installer.database.drivers.sqlite'),
                        'mysql' => __('installer.database.drivers.mysql'),
                    ]"
                />

                @if ($dbConnection !== 'sqlite')
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input wire:model="dbHost" :label="__('installer.database.host')" required />
                        <x-ui.input wire:model="dbPort" :label="__('installer.database.port')" required />
                    </div>
                    <x-ui.input wire:model="dbDatabase" :label="__('installer.database.database')" required />
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input wire:model="dbUsername" :label="__('installer.database.username')" required />
                        <x-ui.input wire:model="dbPassword" type="password" :label="__('installer.database.password')" />
                    </div>
                @else
                    <p class="rounded-md bg-zinc-50 dark:bg-zinc-900 p-3 text-sm text-zinc-600 dark:text-zinc-400">
                        {!! __('installer.database.sqlite_notice', ['path' => '<code class="font-mono text-xs">database/database.sqlite</code>']) !!}
                    </p>
                @endif
            </div>
        @endif

        @if ($step === 4)
            <h2 class="mb-4 text-lg font-medium">{{ __('installer.admin_step.heading') }}</h2>
            <div class="space-y-4">
                <x-ui.input wire:model="adminName" :label="__('installer.admin_step.fields.name')" required />
                <x-ui.input wire:model="adminEmail" type="email" :label="__('installer.admin_step.fields.email')" required />
                <x-ui.input wire:model="adminPassword" type="password" :label="__('installer.admin_step.fields.password')" required :hint="__('installer.admin_step.fields.password_hint')" />
                <x-ui.input wire:model="adminPasswordConfirmation" type="password" :label="__('installer.admin_step.fields.password_confirmation')" required />
            </div>
        @endif

        @if ($step === 5)
            @php($allModulesOn = collect($modules)->every(fn ($v) => (bool) $v))
            @php($selectedCount = collect($modules)->filter()->count())
            @php($totalModules = count($this->moduleList))

            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium">{{ __('installer.modules.heading') }}</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('installer.modules.subtitle') }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">
                        {{ $selectedCount }} / {{ $totalModules }}
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="toggleAllModules"
                    class="shrink-0 inline-flex items-center gap-1.5 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1.5 text-xs font-medium text-zinc-700 dark:text-zinc-200 hover:border-hk-primary-400 hover:text-hk-primary-700 dark:hover:text-hk-primary-300 transition"
                >
                    @if ($allModulesOn)
                        {{ __('installer.modules.deselect_all') }}
                    @else
                        {{ __('installer.modules.select_all') }}
                    @endif
                </button>
            </div>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($this->moduleList as $key => $module)
                    <label @class([
                        'flex items-start gap-3 rounded-md border p-3 cursor-pointer transition',
                        'border-hk-primary-400 bg-hk-primary-50/50 dark:bg-hk-primary-500/10' => ! empty($modules[$key]),
                        'border-zinc-200 dark:border-zinc-800 hover:border-hk-primary-400' => empty($modules[$key]),
                    ])>
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
                <x-ui.button variant="secondary" wire:click="back" wire:loading.attr="disabled">{{ __('installer.buttons.back') }}</x-ui.button>
            @else
                <span></span>
            @endif

            @if ($step < $this->totalSteps)
                <x-ui.button wire:click="next" wire:loading.attr="disabled">{{ __('installer.buttons.continue') }}</x-ui.button>
            @else
                <x-ui.button
                    wire:click="startInstall"
                    :loading="$installing"
                    wire:loading.attr="disabled"
                    wire:target="startInstall,runStep"
                >
                    {{ __('installer.buttons.install') }}
                </x-ui.button>
            @endif
        </div>
    </x-ui.card>

    {{-- Real install progress overlay. Each step is a real Livewire round-trip
         driven by runStep(); the UI reflects exactly what's happening on the
         server, including per-step durations, success messages and any
         failure message captured from the thrown exception. --}}
    @php($hasFailedStep = collect($progressSteps)->contains(fn ($s) => $s['status'] === 'failed'))
    <div
        x-data="{
            open: @js(! empty($progressSteps)),
            running: false,
            async runAll() {
                if (this.running) return;
                this.running = true;
                this.open = true;
                let safety = 50;
                while (safety-- > 0) {
                    let result;
                    try {
                        result = await $wire.runStep();
                    } catch (e) {
                        // Network or server error — let Livewire show its own
                        // toast/error. Keep overlay open so user can read what
                        // got done so far and retry.
                        this.running = false;
                        return;
                    }
                    if (!result || result.done) {
                        this.running = false;
                        if (result && result.redirect && !result.failed) {
                            // Slight pause so the user sees the final tick.
                            setTimeout(() => { window.location.href = result.redirect; }, 600);
                        }
                        return;
                    }
                }
                this.running = false;
            },
            close() { this.open = false; },
        }"
        x-on:hk-install-started.window="runAll()"
        wire:ignore.self
    >
        <div
            x-show="open"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 backdrop-blur-sm p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="install-progress-title"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-900 shadow-2xl ring-1 ring-zinc-200 dark:ring-zinc-800 p-6">
                <div class="flex items-center gap-3">
                    @if ($installComplete)
                        <span class="flex size-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @elseif ($hasFailedStep)
                        <span class="flex size-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @else
                        <span class="relative flex size-10 items-center justify-center rounded-full bg-hk-primary-50 dark:bg-hk-primary-500/10">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-hk-primary-400/40"></span>
                            <svg class="relative size-5 text-hk-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </span>
                    @endif
                    <div>
                        <h3 id="install-progress-title" class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                            @if ($installComplete)
                                {{ __('installer.progress.success_title') }}
                            @elseif ($hasFailedStep)
                                {{ __('installer.progress.failed_title') }}
                            @else
                                {{ __('installer.progress.title') }}
                            @endif
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            @if ($installComplete)
                                {{ __('installer.progress.success_hint') }}
                            @elseif ($hasFailedStep)
                                {{ __('installer.progress.failed_hint') }}
                            @else
                                {{ __('installer.progress.subtitle') }}
                            @endif
                        </p>
                    </div>
                </div>

                @php($total = max(1, count($progressSteps)))
                @php($doneCount = collect($progressSteps)->where('status', 'done')->count())
                @php($percent = (int) round(($doneCount / $total) * 100))

                <div class="mt-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 tabular-nums">
                            {{ $doneCount }} / {{ $total }}
                        </span>
                        <span class="text-xs font-mono text-zinc-500 tabular-nums">{{ $percent }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div
                            @class([
                                'h-full rounded-full transition-[width] duration-300 ease-out',
                                'bg-gradient-to-r from-hk-primary-500 to-hk-primary-600' => ! $hasFailedStep,
                                'bg-gradient-to-r from-red-500 to-red-600' => $hasFailedStep,
                            ])
                            style="width: {{ $percent }}%"
                        ></div>
                    </div>
                </div>

                <ul class="mt-5 max-h-72 overflow-y-auto space-y-1.5 pr-1">
                    @foreach ($progressSteps as $s)
                        <li @class([
                            'flex items-start gap-3 rounded-lg px-3 py-2 text-sm transition-colors',
                            'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200' => $s['status'] === 'done',
                            'bg-hk-primary-50 dark:bg-hk-primary-500/10 text-hk-primary-900 dark:text-hk-primary-100' => $s['status'] === 'running',
                            'bg-red-50 dark:bg-red-950/40 text-red-900 dark:text-red-200' => $s['status'] === 'failed',
                            'text-zinc-500 dark:text-zinc-400' => $s['status'] === 'pending',
                        ])>
                            <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center">
                                @if ($s['status'] === 'done')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-emerald-500">
                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif ($s['status'] === 'running')
                                    <svg class="size-4 animate-spin text-hk-primary-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                @elseif ($s['status'] === 'failed')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-red-500">
                                        <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="block size-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></span>
                                @endif
                            </span>
                            <div class="min-w-0 grow">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-medium truncate">{{ $s['label'] }}</span>
                                    @if (! is_null($s['duration']))
                                        <span class="shrink-0 text-[10px] font-mono text-zinc-500 tabular-nums">{{ number_format($s['duration'], 2) }}s</span>
                                    @endif
                                </div>
                                @if (! empty($s['message']))
                                    <p @class([
                                        'mt-0.5 text-xs break-words',
                                        'text-red-700 dark:text-red-300 font-mono whitespace-pre-wrap' => $s['status'] === 'failed',
                                        'text-zinc-600 dark:text-zinc-400' => $s['status'] !== 'failed',
                                    ])>{{ $s['message'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5 flex items-center justify-between gap-3">
                    @if ($hasFailedStep)
                        <button
                            type="button"
                            wire:click="resetInstallProgress"
                            x-on:click="close()"
                            class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100"
                        >
                            {{ __('installer.buttons.back') }}
                        </button>
                        <x-ui.button
                            wire:click="startInstall"
                            wire:loading.attr="disabled"
                            wire:target="startInstall,runStep"
                        >
                            {{ __('installer.progress.retry') }}
                        </x-ui.button>
                    @elseif ($installComplete)
                        <span></span>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">
                            {{ __('installer.progress.success_hint') }}
                        </p>
                    @else
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 italic">
                            {{ __('installer.progress.hint') }}
                        </p>
                        <span></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
