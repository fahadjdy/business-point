<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasGlobalSoftDeletes, LogsAudit;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'blood_group',
        'gender',
        'password',
        'is_active',
    ];

    protected $appends = ['photo_url'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function photo()
    {
        return $this->morphOne(Media::class, 'model')->latest();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset($this->photo->file_path) : null;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function skills()
    {
        return $this->belongsToMany(Tag::class, 'user_skills');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }
}
