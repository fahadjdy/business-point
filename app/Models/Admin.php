<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'user_id',
        'is_super_admin',
        'deleted_by',
        'delete_reason'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photo()
    {
        return $this->morphOne(Media::class, 'model')->latest();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset($this->photo->file_path) : null;
    }

    protected $appends = ['photo_url'];
}