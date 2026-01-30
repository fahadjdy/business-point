<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopProduct extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'compare_price',
        'is_active',
        'deleted_by',
        'delete_reason'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(ShopProductCategory::class, 'category_id');
    }
}
