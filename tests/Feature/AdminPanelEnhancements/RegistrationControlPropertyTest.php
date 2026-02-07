<?php

namespace Tests\Feature\AdminPanelEnhancements;

use Tests\TestCase;
use App\Models\Setting;
use App\Models\Admin;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Feature: admin-panel-enhancements, Property 2: Registration Control State Management
 * 
 * Property: For any registration permission setting, when "Allow Self Registration" is toggled, 
 * the frontend should show/hide registration forms and create account options consistently 
 * with the setting state.
 * 
 * Validates: Requirements 2.1, 2.2, 2.3
 */
class RegistrationControlPropertyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SettingService $settingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingService = app(SettingService::class);
        
        // Seed basic settings
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
    }

    public function test_registration_control_state_consistency_across_endpoints()
    {
        // Test registration control with random states for property testing
        for ($i = 0; $i < 25; $i++) {
            $allowRegistration = $this->faker->boolean;

            // Set registration permission
            $this->settingService->setVal('allow_self_registration', $allowRegistration ? '1' : '0');

            // Test API consistency - maintenance status endpoint includes registration status
            $response = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $response->assertStatus(200);
            $data = $response->json('data');
            
            $this->assertEquals($allowRegistration, $data['allow_registration'],
                "Maintenance status endpoint: Allow registration should be {$allowRegistration}");

            // Test company details endpoint includes registration status
            $companyResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/company-details');
            
            $companyResponse->assertStatus(200);
            $companyData = $companyResponse->json('data');
            
            $this->assertEquals($allowRegistration, $companyData['allow_registration'],
                "Company details endpoint: Allow registration should be {$allowRegistration}");

            // Test admin settings endpoint
            $admin = $this->createAdminUser();
            $settingsResponse = $this->actingAs($admin, 'admin')->getJson('/admin/settings-data');
            $settingsResponse->assertStatus(200);
            
            $settingsData = $settingsResponse->json('data');
            $this->assertEquals($allowRegistration, $settingsData['allow_registration'],
                "Admin settings endpoint: Allow registration should be {$allowRegistration}");

            // Verify service method consistency
            $serviceResult = $this->settingService->getAllowRegistration();
            $this->assertEquals($allowRegistration, $serviceResult,
                "Service method should return {$allowRegistration}");

            // Verify model static method consistency
            $modelResult = Setting::getAllowRegistration();
            $this->assertEquals($allowRegistration, $modelResult,
                "Model static method should return {$allowRegistration}");
        }
    }

    public function test_registration_control_toggle_behavior()
    {
        $admin = $this->createAdminUser();

        // Test toggling registration control multiple times
        for ($i = 0; $i < 15; $i++) {
            $newState = $this->faker->boolean;

            // Update registration control via admin API
            $response = $this->actingAs($admin, 'admin')->putJson('/admin/settings-data/allow_self_registration', [
                'value' => $newState ? '1' : '0'
            ]);
            $response->assertStatus(200);

            // Verify the state is immediately reflected in all endpoints
            $statusResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $statusData = $statusResponse->json('data');
            
            $this->assertEquals($newState, $statusData['allow_registration'],
                "After toggle, allow registration should be {$newState}");

            // Verify company details endpoint reflects the change
            $companyResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/company-details');
            
            $companyData = $companyResponse->json('data');
            $this->assertEquals($newState, $companyData['allow_registration'],
                "Company details should reflect new registration state: {$newState}");

            // Verify database state
            $dbSetting = Setting::where('key', 'allow_self_registration')->first();
            $this->assertEquals($newState ? '1' : '0', $dbSetting->value,
                "Database should reflect the new registration state");

            // Verify service method reflects the change immediately
            $serviceResult = $this->settingService->getAllowRegistration();
            $this->assertEquals($newState, $serviceResult,
                "Service method should immediately return new state: {$newState}");
        }
    }

    public function test_registration_control_default_behavior()
    {
        // Test default behavior when setting doesn't exist
        Setting::where('key', 'allow_self_registration')->delete();

        // Service should return default value (true)
        $serviceResult = $this->settingService->getAllowRegistration();
        $this->assertTrue($serviceResult, "Service should return true as default when setting doesn't exist");

        // Model should return default value (true)
        $modelResult = Setting::getAllowRegistration();
        $this->assertTrue($modelResult, "Model should return true as default when setting doesn't exist");

        // API endpoints should return default value
        $response = $this->withHeaders([
            'X-Api-Key' => config('services.auth_api_key')
        ])->getJson('/api/v1/maintenance-status');
        
        $data = $response->json('data');
        $this->assertTrue($data['allow_registration'], "API should return true as default");
    }

    public function test_registration_control_with_various_value_formats()
    {
        // Test different value formats that should be interpreted as boolean
        $testCases = [
            ['1', true],
            ['0', false],
            [1, true],
            [0, false],
            ['true', true],
            ['false', false],
            ['yes', true],
            ['no', false],
        ];

        foreach ($testCases as [$value, $expected]) {
            // Set the value directly in database
            Setting::where('key', 'allow_self_registration')->update(['value' => $value]);

            // Clear any caching
            \Illuminate\Support\Facades\Cache::forget('setting_allow_self_registration');

            // Test service interpretation
            $serviceResult = $this->settingService->getAllowRegistration();
            $this->assertEquals($expected, $serviceResult,
                "Service should interpret '{$value}' as " . ($expected ? 'true' : 'false'));

            // Test model interpretation
            $modelResult = Setting::getAllowRegistration();
            $this->assertEquals($expected, $modelResult,
                "Model should interpret '{$value}' as " . ($expected ? 'true' : 'false'));

            // Test API response
            $response = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $data = $response->json('data');
            $this->assertEquals($expected, $data['allow_registration'],
                "API should interpret '{$value}' as " . ($expected ? 'true' : 'false'));
        }
    }

    /**
     * Create an admin user for testing
     */
    private function createAdminUser(): Admin
    {
        // Create a user first with required phone field
        $user = User::factory()->create([
            'phone' => $this->faker->phoneNumber()
        ]);
        
        return Admin::factory()->create([
            'user_id' => $user->id
        ]);
    }
}