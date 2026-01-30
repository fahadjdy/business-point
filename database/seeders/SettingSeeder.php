<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Repositories\SettingRepository;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Branding & General
            [
                'key' => 'app_name',
                'value' => 'Business Point',
                'type' => 'string',
                'description' => 'The public name of the application.'
            ],
            [
                'key' => 'brand_name',
                'value' => 'Business Point',
                'type' => 'string',
                'description' => 'The brand name displayed in the header and footer.'
            ],
            [
                'key' => 'site_logo',
                'value' => '/assets/media/logos/logo-bp.png',
                'type' => 'string',
                'description' => 'URL to the company logo.'
            ],
            [
                'key' => 'site_favicon',
                'value' => '/favicon.ico',
                'type' => 'string',
                'description' => 'URL to the site favicon.'
            ],
            [
                'key' => 'brand_tagline',
                'value' => 'Your Success Partner',
                'type' => 'string',
                'description' => 'Tagline displayed below the logo.'
            ],
            [
                'key' => 'about_company',
                'value' => 'Business Point is a comprehensive solution for managing your business needs efficiently.',
                'type' => 'string',
                'description' => 'Short company description located in footer or about page.'
            ],
            [
                'key' => 'app_email',
                'value' => 'support@businesspoint.com',
                'type' => 'string',
                'description' => 'Contact email for support.'
            ],
            [
                'key' => 'app_phone',
                'value' => '+1 234 567 890',
                'type' => 'string',
                'description' => 'Contact phone number.'
            ],

            // Maintenance
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Toggle site maintenance mode.'
            ],
            [
                'key' => 'maintenance_note',
                'value' => 'We are currently performing scheduled maintenance. Please check back later.',
                'type' => 'string',
                'description' => 'Message displayed during maintenance.'
            ],
            // Notifications & Logic
            [
                'key' => 'notify_signup',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Notify admin when a new user/vendor signs up.'
            ],
            [
                'key' => 'auto_verify_vendors',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Automatically verify new vendor accounts.'
            ],
            [
                'key' => 'allow_self_registration',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow users/vendors to register themselves.'
            ],
            [
                'key' => 'vendor_commission',
                'value' => '10',
                'type' => 'number',
                'description' => 'Default commission percentage for vendors.'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
