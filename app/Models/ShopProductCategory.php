<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopProductCategory extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(ShopProduct::class, 'category_id');
    }
}
