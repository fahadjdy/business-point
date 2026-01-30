<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barber extends Model
{
    use HasFactory, HasGlobalSoftDeletes;

    protected $fillable = [
        'vendor_id',
        'shop_name',
        'services',
        'address',
        'deleted_by',
        'delete_reason'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
