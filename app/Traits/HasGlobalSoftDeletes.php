<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasGlobalSoftDeletes
{
    use SoftDeletes;

    /**
     * Boot the trait.
     */
    protected static function bootHasGlobalSoftDeletes()
    {
        static::deleting(function ($model) {
            if (!$model->isForceDeleting() && auth()->check()) {
                $model->deleted_by = auth()->id();
            }
        });
    }

    /**
     * Get the deleted by user.
     */
    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}
