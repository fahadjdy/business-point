<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyContact extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'name',
        'contact_number',
        'icon',
        'badge',
        'color',
        'sort_order',
        'description',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    public function photo()
    {
        return $this->morphOne(Media::class, 'model');
    }
}
