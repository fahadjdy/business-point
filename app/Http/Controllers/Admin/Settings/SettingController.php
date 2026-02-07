<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAllSettings()
        ]);
    }

    public function update(Request $request)
    {
        foreach ($request->all() as $key => $value) {

            // Handle File Uploads
            if ($request->hasFile($key) && ($key === 'site_logo' || $key === 'site_favicon')) {
                $file = $request->file($key);
                $path = $file->store('uploads/settings', 'public');
                $value = Storage::url($path);
            }

            $this->service->updateSetting($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Get maintenance status
     */
    public function maintenanceStatus()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getMaintenanceStatus()
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => ['key' => $key, 'value' => $validated['value']]
        ]);
    }
}
