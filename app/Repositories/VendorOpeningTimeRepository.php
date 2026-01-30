<?php

namespace App\Repositories;

use App\Models\VendorOpeningTime;

class VendorOpeningTimeRepository extends BaseRepository
{
    public function __construct(VendorOpeningTime $openingTime)
    {
        parent::__construct($openingTime);
    }

    /**
     * Get opening times for a specific vendor.
     */
    public function getByVendorId(int $vendorId)
    {
        return $this->model->where('vendor_id', $vendorId)->get();
    }
}
