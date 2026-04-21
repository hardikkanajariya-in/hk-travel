<?php

namespace App\Core\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use InvalidArgumentException;

/**
 * Single accessor for application disks.
 *
 * Currently only `local` (private) and `public` are exposed. Other drivers
 * (s3, spaces, gcs) are listed as "scaffolded" in config/hk.php so the
 * admin UI can show them as "Coming soon" without breaking when selected.
 */
class StorageManager
{
    public function __construct(protected FilesystemManager $files) {}

    public function disk(?string $name = null): Filesystem
    {
        $name ??= config('hk.storage.default_disk', 'local');

        $available = (array) config('hk.storage.available_drivers', ['local']);

        if (! in_array($name, $available, true) && ! in_array($name, ['public', 'local'], true)) {
            throw new InvalidArgumentException("Storage disk [$name] is not enabled in this release.");
        }

        return $this->files->disk($name);
    }

    public function publicDisk(): Filesystem
    {
        return $this->files->disk(config('hk.storage.public_disk', 'public'));
    }

    /** @return array<int, string> */
    public function availableDrivers(): array
    {
        return (array) config('hk.storage.available_drivers', ['local']);
    }

    /** @return array<int, string> */
    public function scaffoldedDrivers(): array
    {
        return (array) config('hk.storage.scaffolded_drivers', []);
    }
}
