<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, HasGlobalSoftDeletes;

    protected $fillable = ['name', 'deleted_by', 'delete_reason'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
