<?php

namespace App\Concerns;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * HK Travel default audit-log trait.
 *
 * Wraps spatie/laravel-activitylog with sane defaults — only logs
 * changed attributes, drops timestamps and password/token fields, and
 * skips empty diffs. Models can override `auditAttributes()` to control
 * which attributes are tracked, and `auditExcluded()` to add to the
 * baseline ignore list.
 */
trait HasAuditLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->auditAttributes())
            ->logOnlyDirty()
            ->useLogName($this->auditLogName());
    }

    /** @return array<int, string> */
    protected function auditAttributes(): array
    {
        $excluded = array_merge(
            ['password', 'remember_token', 'api_token', 'two_factor_secret', 'two_factor_recovery_codes', 'created_at', 'updated_at', 'deleted_at'],
            $this->auditExcluded(),
        );

        $columns = property_exists($this, 'fillable') && $this->fillable !== []
            ? $this->fillable
            : array_keys($this->getAttributes());

        return array_values(array_diff($columns, $excluded));
    }

    /** @return array<int, string> */
    protected function auditExcluded(): array
    {
        return property_exists($this, 'auditExcluded') ? (array) $this->auditExcluded : [];
    }

    protected function auditLogName(): string
    {
        return strtolower(class_basename(static::class));
    }
}
