<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Cache;

/**
 * @property \App\Repositories\SettingRepository $repository
 */
class SettingService extends BaseService
{
    public function __construct(SettingRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get a setting value by key with caching.
     */
    public function getVal(string $key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = $this->repository->getByKey($key);
            if (!$setting)
                return $default;

            return $this->formatValue($setting->type, $setting->value);
        });
    }

    /**
     * Get all settings as key-value pairs.
     */
    public function getAllSettings(): array
    {
        $settings = $this->repository->all()->pluck('value', 'key')->toArray();

        // Convert relative paths to absolute URLs for assets
        $assetKeys = ['site_logo', 'site_favicon'];
        foreach ($assetKeys as $key) {
            if (isset($settings[$key]) && !empty($settings[$key])) {
                if (!str_starts_with($settings[$key], 'http') && !str_starts_with($settings[$key], 'data:')) {
                    $settings[$key] = asset(ltrim($settings[$key], '/'));
                }
            }
        }

        // Add maintenance mode and registration status for API compatibility
        $settings['maintenance_mode'] = $this->getMaintenanceMode();
        $settings['maintenance_note'] = $this->getMaintenanceNote();
        $settings['allow_registration'] = $this->getAllowRegistration();

        return $settings;
    }

    /**
     * Get maintenance mode status
     */
    public function getMaintenanceMode(): bool
    {
        return $this->getVal('maintenance_mode', false);
    }

    /**
     * Get maintenance note
     */
    public function getMaintenanceNote(): string
    {
        return $this->getVal('maintenance_note', 'We are currently performing scheduled maintenance. Please check back later.');
    }

    /**
     * Get allow registration status
     */
    public function getAllowRegistration(): bool
    {
        return $this->getVal('allow_self_registration', true);
    }

    /**
     * Get maintenance status for API
     */
    public function getMaintenanceStatus(): array
    {
        return [
            'maintenance_mode' => $this->getMaintenanceMode(),
            'maintenance_note' => $this->getMaintenanceNote(),
            'allow_registration' => $this->getAllowRegistration()
        ];
    }

    /**
     * Update or create a setting.
     */
    public function updateSetting(string $key, $value)
    {
        $this->setVal($key, $value);
    }

    /**
     * Update setting value and clear cache.
     */
    public function setVal(string $key, $value)
    {
        $setting = $this->repository->getByKey($key);
        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : $value]);
            Cache::forget('setting_' . $key);
            return true;
        } else {
            // Create if not exists (optional, depends on preference)
            $this->repository->create([
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'string')
            ]);
            Cache::forget('setting_' . $key);
            return true;
        }
    }

    protected function formatValue(string $type, $value)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
