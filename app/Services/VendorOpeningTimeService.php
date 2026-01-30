<?php

namespace App\Services;

use App\Repositories\VendorOpeningTimeRepository;

/**
 * @property \App\Repositories\VendorOpeningTimeRepository $repository
 */
class VendorOpeningTimeService extends BaseService
{
    public function __construct(VendorOpeningTimeRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Update or create opening times for a vendor.
     */
    public function syncOpeningTimes(int $vendorId, array $times)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($vendorId, $times) {
            $this->repository->model->where('vendor_id', $vendorId)->delete();

            foreach ($times as $time) {
                $time['vendor_id'] = $vendorId;
                $this->repository->create($time);
            }

            return true;
        });
    }
}
