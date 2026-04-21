<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * `hk:make:migration` — module-aware wrapper around `make:migration`.
 *
 * Generates a migration inside `app/Modules/{Module}/database/migrations`
 * so the file ships with the module folder and is auto-loaded by
 * ModuleServiceProvider when the module is enabled. Falls back to the
 * default migrations directory if --module is omitted.
 *
 * This wrapper is the only sanctioned way to create migrations in HK
 * Travel — never call `make:migration` directly so module ownership and
 * the path convention stay enforced.
 */
#[Signature('hk:make:migration
    {name : Migration name in snake_case, e.g. create_tours_table}
    {--module= : Module key under app/Modules to scope the migration to}
    {--create= : Table to be created}
    {--table= : Table to migrate}')]
#[Description('Create a migration scoped to an HK Travel module (or app-level if --module omitted).')]
class MakeModuleMigration extends Command
{
    public function handle(): int
    {
        $name = (string) $this->argument('name');
        $module = $this->option('module');

        $params = ['name' => $name];

        if ($create = $this->option('create')) {
            $params['--create'] = $create;
        }

        if ($table = $this->option('table')) {
            $params['--table'] = $table;
        }

        if ($module) {
            $module = Str::studly($module);
            $path = base_path('app/Modules/'.$module.'/database/migrations');

            if (! is_dir($path) && ! mkdir($path, 0775, true) && ! is_dir($path)) {
                $this->error("Could not create migration directory: $path");

                return self::FAILURE;
            }

            $params['--path'] = 'app/Modules/'.$module.'/database/migrations';
            $params['--realpath'] = false;
        }

        $exit = Artisan::call('make:migration', $params, $this->output);

        return $exit === 0 ? self::SUCCESS : self::FAILURE;
    }
}
