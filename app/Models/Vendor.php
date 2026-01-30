<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use App\Traits\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory, HasGlobalSoftDeletes, LogsAudit;

    protected $fillable = [
        'user_id',
        'vendor_type',
        'is_verified',
        'is_public',
        'verification_status',
        'status',
        'latitude',
        'longitude',
        'deleted_by',
        'delete_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

    public function openingTimes()
    {
        return $this->hasMany(VendorOpeningTime::class);
    }
}
