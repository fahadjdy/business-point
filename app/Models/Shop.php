<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'vendor_id',
        'shop_name',
        'category',
        'description',
        'address',
        'price_display_enabled',
        'deleted_by',
        'delete_reason'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function products()
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function productCategories()
    {
        return $this->hasMany(ShopProductCategory::class);
    }
}
