<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;

class UserSetting extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
