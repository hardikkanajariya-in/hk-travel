<?php

namespace App\Core\Modules;

/**
 * Contract every HK Travel module's manifest must satisfy.
 *
 * A module is a self-contained feature folder under app/Modules/{Name}
 * that boots only when its enable flag in config/hk-modules.php is true.
 */
interface ModuleContract
{
    public function key(): string;

    public function name(): string;

    public function version(): string;

    /** @return array<int, string> */
    public function permissions(): array;

    public function publicRoutesPath(): ?string;

    public function adminRoutesPath(): ?string;

    public function migrationsPath(): ?string;

    public function viewsPath(): ?string;

    public function langPath(): ?string;

    public function viewNamespace(): ?string;

    /** Optional service provider class to defer additional bootstrapping. */
    public function provider(): ?string;
}
