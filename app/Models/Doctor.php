<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasGlobalSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory, HasGlobalSoftDeletes;

    protected $fillable = [
        'vendor_id',
        'clinic_name',
        'specialization',
        'qualification',
        'clinic_address',
        'experience_years',
        'deleted_by',
        'delete_reason'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
