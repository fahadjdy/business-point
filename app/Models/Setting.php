<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'key',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Get maintenance mode status
     */
    public static function getMaintenanceMode(): bool
    {
        $setting = self::where('key', 'maintenance_mode')->first();
        if (!$setting) {
            return false; // Default to not in maintenance mode
        }
        
        // Handle various boolean representations
        $value = strtolower(trim($setting->value));
        return in_array($value, ['1', 'true', 'yes']) || $value === true || $value === 1;
    }

    /**
     * Get allow registration status
     */
    public static function getAllowRegistration(): bool
    {
        $setting = self::where('key', 'allow_self_registration')->first();
        if (!$setting) {
            return true; // Default to allowing registration
        }
        
        // Handle various boolean representations
        $value = strtolower(trim($setting->value));
        return in_array($value, ['1', 'true', 'yes']) || $value === true || $value === 1;
    }

    /**
     * Get maintenance note
     */
    public static function getMaintenanceNote(): string
    {
        $setting = self::where('key', 'maintenance_note')->first();
        return $setting ? $setting->value : 'We are currently performing scheduled maintenance. Please check back later.';
    }

    /**
     * Get a setting value by key with optional default
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
