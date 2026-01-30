<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditLogObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->log($model, 'create', null, $this->getFilteredAttributes($model));
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $old = $model->getOriginal();
        $new = $model->getAttributes();

        $excludes = method_exists($model, 'getAuditExcludes') ? $model->getAuditExcludes() : [];

        $changes = [];
        $original = [];

        foreach ($model->getChanges() as $key => $value) {
            if (in_array($key, $excludes))
                continue;

            $changes[$key] = $value;
            $original[$key] = $old[$key] ?? null;
        }

        if (!empty($changes)) {
            $this->log($model, 'update', $original, $changes);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'delete' : 'delete';
        // For soft deletes, it's still 'delete' in our enum.
        $this->log($model, 'delete', $this->getFilteredAttributes($model), null);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->log($model, 'restore', null, $this->getFilteredAttributes($model));
    }

    /**
     * Log the action to the database.
     */
    protected function log(Model $model, string $action, ?array $oldValue, ?array $newValue): void
    {
        $user = auth()->user();

        AuditLog::create([
            'request_id' => (string) Str::uuid(), // In a real app, this might come from a request middleware
            'actor_type' => $user ? ($user->admin ? 'admin' : 'user') : 'system',
            'actor_id' => $user?->id,
            'module' => method_exists($model, 'getAuditModule') ? $model->getAuditModule() : strtolower(class_basename($model)),
            'entity_type' => get_class($model),
            'entity_id' => $model->id,
            'action' => $action,
            'status' => 'success',
            'old_values' => $oldValue,
            'new_values' => $newValue,
            'metadata' => [
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
            ]
        ]);
    }

    /**
     * Get filtered attributes for the model.
     */
    protected function getFilteredAttributes(Model $model): array
    {
        $attributes = $model->getAttributes();
        $excludes = method_exists($model, 'getAuditExcludes') ? $model->getAuditExcludes() : [];

        return array_diff_key($attributes, array_flip($excludes));
    }
}
