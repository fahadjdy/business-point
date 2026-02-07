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
     * @deprecated Use updateStatus instead
     */
    public function verifyVendor(int $id, string $status = 'verified')
    {
        return $this->updateStatus($id, $status);
    }

    /**
     * Update vendor status (approve/reject).
     */
    public function updateStatus(int $id, string $status, ?string $reason = null)
    {
        $updateData = [
            'verification_status' => $status,
        ];

        if ($status === 'approved') {
            $updateData['is_verified'] = true;
            $updateData['is_active'] = true; // Make them live
        } elseif ($status === 'rejected') {
            $updateData['is_verified'] = false;
            $updateData['is_active'] = false;
        }

        if ($reason) {
            // Log reason if needed, or store in a separate column if schema supports
            // Current schema has 'delete_reason' but not 'rejection_reason'.
            // For now we might just log it via audit or ignore if no column.
        }

        return $this->update($id, $updateData);
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
        $loaders = [$type, 'user', 'openingTimes', 'tags'];
        if ($type === 'shop') {
            $loaders[] = 'shop.category';
        }

        return $vendor->load($loaders);
    }

    /**
     * Register a new vendor with its sub-profile.
     */
    public function registerVendor(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $type = $data['type']; // frontend sends 'type', model expects 'vendor_type'
            
            // 1. Create Vendor Record (Parent)
            $vendor = $this->repository->create([
                'user_id' => auth()->id(),
                'vendor_type' => $type,
                'business_name' => $data['name'],
                'description' => $data['description'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'website' => $data['website'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'is_active' => false,
                'is_verified' => false,
                'verification_status' => 'pending',
                'status' => 'active'
            ]);

            // 2. Handle sub-profile
            switch ($type) {
                case 'shop':
                    \App\Models\Shop::create([
                        'vendor_id' => $vendor->id,
                        'shop_name' => $data['name'],
                        'shop_category_id' => $data['shop_category_id'] ?? null,
                        'description' => $data['description'],
                        'address' => $data['address'] . ', ' . $data['city'] . ', ' . $data['state'],
                    ]);
                    break;
                case 'doctor':
                    \App\Models\Doctor::create([
                        'vendor_id' => $vendor->id,
                        'clinic_name' => $data['name'],
                        'specialization' => $data['services'] ?? 'General',
                        'qualification' => 'N/A',
                        'clinic_address' => $data['address'],
                    ]);
                    break;
                case 'barber':
                    \App\Models\Barber::create([
                        'vendor_id' => $vendor->id,
                        'shop_name' => $data['name'],
                        'services' => $data['services'] ?? null,
                        'address' => $data['address'],
                    ]);
                    break;
            }

            // 3. Store Opening Times
            if (isset($data['operating_days']) && is_array($data['operating_days'])) {
                $dayMapping = [
                    'monday' => 'mon', 'tuesday' => 'tue', 'wednesday' => 'wed',
                    'thursday' => 'thu', 'friday' => 'fri', 'saturday' => 'sat', 'sunday' => 'sun'
                ];

                foreach ($data['operating_days'] as $day) {
                    $businessHours = $data['business_hours'] ?? [];
                    $openKey = $day . '_open';
                    $closeKey = $day . '_close';
                    $dbDay = $dayMapping[strtolower($day)] ?? substr(strtolower($day), 0, 3);
                    
                    if (isset($businessHours[$openKey]) && isset($businessHours[$closeKey])) {
                        \App\Models\VendorOpeningTime::create([
                            'vendor_id' => $vendor->id,
                            'day_of_week' => $dbDay,
                            'open_time' => $businessHours[$openKey],
                            'close_time' => $businessHours[$closeKey],
                            'is_closed' => false
                        ]);
                    }
                }
            }

            return $vendor->load($type);
        });
    }
    /**
     * Update vendor profile information.
     */
    public function updateProfile(int $id, array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $vendor = $this->findById($id);
            
            // Allowed fields for vendor update
            $fillable = ['business_name', 'description', 'phone', 'email', 'website', 'address', 'city', 'state'];
            $updateData = array_intersect_key($data, array_flip($fillable));
            
            $vendor->update($updateData);
            
            // Sync specialized sub-models (Shop, Doctor, Barber)
            $type = $vendor->vendor_type;
            $syncData = [];
            
            if (isset($data['business_name'])) {
                $syncData[$type === 'doctor' ? 'clinic_name' : 'shop_name'] = $data['business_name'];
            }
            
            if (isset($data['address']) || isset($data['city']) || isset($data['state'])) {
                $fullAddress = ($data['address'] ?? $vendor->address);
                if ($type === 'shop') {
                    $fullAddress .= ', ' . ($data['city'] ?? $vendor->city) . ', ' . ($data['state'] ?? $vendor->state);
                }
                $syncData[$type === 'doctor' ? 'clinic_address' : 'address'] = $fullAddress;
            }

            if (!empty($syncData)) {
                $vendor->$type()->update($syncData);
            }

            return $vendor->refresh();
        });
    }
}
