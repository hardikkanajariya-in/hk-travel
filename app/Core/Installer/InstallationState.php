<?php

namespace App\Core\Installer;

use Illuminate\Filesystem\Filesystem;

/**
 * Single source of truth for "is this app installed?".
 *
 * Presence of storage/app/{lock_file} means the wizard ran successfully.
 * Public routes that need the wizard (or want to redirect away from it
 * post-install) consult this class via app(InstallationState::class).
 */
class InstallationState
{
    public function __construct(protected Filesystem $files) {}

    public function isInstalled(): bool
    {
        return $this->files->exists($this->lockPath());
    }

    public function markInstalled(): void
    {
        $this->files->put($this->lockPath(), json_encode([
            'installed_at' => now()->toIso8601String(),
            'version' => '0.1.0',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function reset(): void
    {
        if ($this->files->exists($this->lockPath())) {
            $this->files->delete($this->lockPath());
        }
    }

    public function lockPath(): string
    {
        return storage_path('app/'.config('hk.installer.lock_file', 'installed.lock'));
    }
}
