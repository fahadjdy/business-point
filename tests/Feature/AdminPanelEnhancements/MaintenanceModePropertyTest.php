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
 * Feature: admin-panel-enhancements, Property 1: Maintenance Mode State Management
 * 
 * Property: For any system configuration, when maintenance mode is toggled, 
 * the frontend should display maintenance page when enabled and normal content when disabled, 
 * while the API should return consistent maintenance status information.
 * 
 * Validates: Requirements 1.1, 1.2, 1.3, 1.5
 */
class MaintenanceModePropertyTest extends TestCase
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

    public function test_maintenance_mode_state_is_consistent_across_all_endpoints()
    {
        // Generate random maintenance mode states and notes for property testing
        for ($i = 0; $i < 10; $i++) {
            $maintenanceMode = $this->faker->boolean;
            $maintenanceNote = $this->faker->sentence;

            // Set maintenance mode state
            $this->settingService->setVal('maintenance_mode', $maintenanceMode ? '1' : '0');
            $this->settingService->setVal('maintenance_note', $maintenanceNote);

            // Test API endpoint consistency with API key
            $response = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $response->assertStatus(200);
            
            $data = $response->json('data');
            $this->assertEquals($maintenanceMode, $data['maintenance_mode'], 
                "Maintenance mode should be {$maintenanceMode} but got {$data['maintenance_mode']}");
            $this->assertEquals($maintenanceNote, $data['maintenance_note'],
                "Maintenance note should match the set value");

            // Test company details endpoint includes maintenance status
            $companyResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/company-details');
            
            $companyResponse->assertStatus(200);
            
            $companyData = $companyResponse->json('data');
            $this->assertEquals($maintenanceMode, $companyData['maintenance_mode'],
                "Company details maintenance mode should be {$maintenanceMode}");
            $this->assertEquals($maintenanceNote, $companyData['maintenance_note'],
                "Company details maintenance note should match");

            // Test admin endpoint consistency
            $admin = $this->createAdminUser();
            $adminResponse = $this->actingAs($admin, 'admin')->getJson('/admin/maintenance-status');
            $adminResponse->assertStatus(200);
            
            $adminData = $adminResponse->json('data');
            $this->assertEquals($maintenanceMode, $adminData['maintenance_mode'],
                "Admin maintenance mode should be {$maintenanceMode}");
            $this->assertEquals($maintenanceNote, $adminData['maintenance_note'],
                "Admin maintenance note should match");

            // Test settings data endpoint
            $settingsResponse = $this->actingAs($admin, 'admin')->getJson('/admin/settings-data');
            $settingsResponse->assertStatus(200);
            
            $settingsData = $settingsResponse->json('data');
            $this->assertEquals($maintenanceMode, $settingsData['maintenance_mode'],
                "Settings data maintenance mode should be {$maintenanceMode}");
        }
    }

    public function test_maintenance_mode_toggle_behavior_is_consistent()
    {
        $admin = $this->createAdminUser();

        // Test toggling maintenance mode multiple times
        for ($i = 0; $i < 5; $i++) {
            $newState = $this->faker->boolean;
            $newNote = $this->faker->sentence;

            // Update maintenance mode via API
            $response = $this->actingAs($admin, 'admin')->putJson('/admin/settings-data/maintenance_mode', [
                'value' => $newState ? '1' : '0'
            ]);
            $response->assertStatus(200);

            // Update maintenance note
            $noteResponse = $this->actingAs($admin, 'admin')->putJson('/admin/settings-data/maintenance_note', [
                'value' => $newNote
            ]);
            $noteResponse->assertStatus(200);

            // Verify the state is immediately reflected in all endpoints
            $statusResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $statusData = $statusResponse->json('data');
            
            $this->assertEquals($newState, $statusData['maintenance_mode'],
                "After toggle, maintenance mode should be {$newState}");
            $this->assertEquals($newNote, $statusData['maintenance_note'],
                "After update, maintenance note should match");

            // Verify database state
            $dbSetting = Setting::where('key', 'maintenance_mode')->first();
            $this->assertEquals($newState ? '1' : '0', $dbSetting->value,
                "Database should reflect the new maintenance mode state");

            $dbNote = Setting::where('key', 'maintenance_note')->first();
            $this->assertEquals($newNote, $dbNote->value,
                "Database should reflect the new maintenance note");
        }
    }

    public function test_registration_control_state_is_consistent()
    {
        // Test registration control with random states
        for ($i = 0; $i < 10; $i++) {
            $allowRegistration = $this->faker->boolean;

            // Set registration permission
            $this->settingService->setVal('allow_self_registration', $allowRegistration ? '1' : '0');

            // Test API consistency
            $response = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/maintenance-status');
            
            $response->assertStatus(200);
            $data = $response->json('data');
            
            $this->assertEquals($allowRegistration, $data['allow_registration'],
                "Allow registration should be {$allowRegistration}");

            // Test company details endpoint
            $companyResponse = $this->withHeaders([
                'X-Api-Key' => config('services.auth_api_key')
            ])->getJson('/api/v1/company-details');
            
            $companyResponse->assertStatus(200);
            $companyData = $companyResponse->json('data');
            
            $this->assertEquals($allowRegistration, $companyData['allow_registration'],
                "Company details allow registration should be {$allowRegistration}");

            // Verify service method consistency
            $serviceResult = $this->settingService->getAllowRegistration();
            $this->assertEquals($allowRegistration, $serviceResult,
                "Service method should return {$allowRegistration}");
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