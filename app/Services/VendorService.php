<?php

namespace App\Services;

use App\Repositories\VendorRepository;

class VendorService extends BaseService
{
    public function __construct(VendorRepository $vendorRepository)
    {
        parent::__construct($vendorRepository);
    }

    /**
     * Verify a vendor and update status.
     */
    public function verifyVendor(int $id, string $status = 'verified')
    {
        return $this->update($id, [
            'is_verified' => $status === 'verified',
            'verification_status' => $status,
        ]);
    }

    /**
     * Get vendor by ID with specialized profile (shop/doctor/barber).
     */
    public function getVendorProfile(int $id)
    {
        $vendor = $this->findById($id);
        if (!$vendor)
            return null;

        $type = $vendor->vendor_type;
        return $vendor->load([$type, 'openingTimes', 'tags']);
    }

    /**
     * Register a new vendor with its sub-profile.
     */
    public function registerVendor(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            $data['is_active'] = false; // Requires admin approval
            $data['is_verified'] = false;

            $vendor = $this->repository->create($data);

            // Handle sub-profile
            $type = $data['vendor_type'];
            $profileData = $data['profile'] ?? [];
            $profileData['vendor_id'] = $vendor->id;

            switch ($type) {
                case 'shop':
                    \App\Models\Shop::create($profileData);
                    break;
                case 'doctor':
                    \App\Models\Doctor::create($profileData);
                    break;
                case 'barber':
                    \App\Models\Barber::create($profileData);
                    break;
            }

            return $vendor->load($type);
        });
    }
}
