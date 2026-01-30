<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->success(
            $this->service->getAllSettings(),
            'Company details retrieved'
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'required',
        ]);

        foreach ($validated['settings'] as $item) {
            $this->service->setVal($item['key'], $item['value']);
        }

        return $this->success(null, 'Settings updated successfully');
    }

    /**
     * Get maintenance status for mobile app compatibility
     */
    public function maintenanceStatus()
    {
        return $this->success(
            $this->service->getMaintenanceStatus(),
            'Maintenance status retrieved'
        );
    }

    /**
     * Update specific setting by key
     */
    public function updateSetting(Request $request, string $key)
    {
        $validated = $request->validate([
            'value' => 'required',
        ]);

        $this->service->setVal($key, $validated['value']);

        return $this->success(
            ['key' => $key, 'value' => $validated['value']],
            'Setting updated successfully'
        );
    }
}
