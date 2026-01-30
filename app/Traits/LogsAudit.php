<?php

namespace App\Traits;

use App\Observers\AuditLogObserver;

trait LogsAudit
{
    /**
     * Boot the trait and register the observer.
     */
    public static function bootLogsAudit(): void
    {
        static::observe(AuditLogObserver::class);
    }

    /**
     * Get the audit module name.
     */
    public function getAuditModule(): string
    {
        return $this->auditModule ?? strtolower(class_basename($this));
    }

    /**
     * Fields that should NOT be audited.
     */
    public function getAuditExcludes(): array
    {
        return array_merge(['updated_at', 'created_at', 'deleted_at'], $this->auditExcludes ?? []);
    }
}
