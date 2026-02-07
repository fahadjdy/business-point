<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
