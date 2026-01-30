<?php

namespace App\Repositories;

use App\Models\Vendor;

class VendorRepository extends BaseRepository
{
    public function __construct(Vendor $vendor)
    {
        parent::__construct($vendor);
    }

    /**
     * Search vendors by type and verification status.
     */
    public function searchVendors(array $params)
    {
        $query = $this->model->newQuery();

        if (isset($params['type'])) {
            $query->where('vendor_type', $params['type']);
        }

        if (isset($params['verified'])) {
            $query->where('is_verified', $params['verified']);
        }

        $query->where('is_public', true);
        $query->where('status', 'active');

        // Geolocation search would go here if needed

        return $query->paginate($params['per_page'] ?? 15);
    }
}
