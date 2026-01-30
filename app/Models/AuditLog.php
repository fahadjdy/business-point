<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'actor_type',
        'actor_id',
        'module',
        'entity_type',
        'entity_id',
        'action',
        'status',
        'old_values',
        'new_values',
        'metadata',
        'error_message'
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'metadata' => 'json',
        'created_at' => 'datetime',
    ];
}
