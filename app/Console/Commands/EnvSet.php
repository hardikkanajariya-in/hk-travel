<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * `hk:env:set KEY=value [KEY2=value2 ...]` — safely upsert .env entries.
 *
 * The installer wizard and admin UI use this command instead of writing
 * to .env directly. Values containing whitespace or `#` are auto-quoted.
 * Existing keys are replaced in place; new keys are appended.
 */
#[Signature('hk:env:set
    {pairs?* : One or more KEY=value pairs to write into .env}
    {--unset=* : Keys to remove from .env}')]
#[Description('Safely upsert (or remove) entries in the .env file. Used by installer + admin UI.')]
class EnvSet extends Command
{
    public function handle(): int
    {
        $envPath = base_path('.env');

        if (! is_file($envPath)) {
            $this->error('.env file not found at '.$envPath);

            return self::FAILURE;
        }

        $content = (string) file_get_contents($envPath);

        foreach ((array) $this->argument('pairs') as $pair) {
            if (! str_contains($pair, '=')) {
                $this->warn("Skipping malformed pair: $pair");

                continue;
            }
            [$key, $value] = explode('=', $pair, 2);
            $content = $this->upsert($content, trim($key), $value);
        }

        foreach ((array) $this->option('unset') as $key) {
            $content = $this->remove($content, trim($key));
        }

        file_put_contents($envPath, $content);
        $this->info('.env updated.');

        return self::SUCCESS;
    }

    protected function upsert(string $content, string $key, string $value): string
    {
        $line = $key.'='.$this->quote($value);
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $line, $content);
        }

        return rtrim($content, "\r\n")."\n".$line."\n";
    }

    protected function remove(string $content, string $key): string
    {
        $pattern = '/^'.preg_quote($key, '/').'=.*\R?/m';

        return preg_replace($pattern, '', $content);
    }

    protected function quote(string $value): string
    {
        if ($value === '' || preg_match('/^[A-Za-z0-9_\-\.\/:]+$/', $value)) {
            return $value;
        }

        return '"'.Str::of($value)->replace('"', '\\"').'"';
    }
}
