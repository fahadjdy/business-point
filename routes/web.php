<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Vendors\VendorController;
use App\Http\Controllers\Admin\AuditLogs\AuditLogController;
use App\Http\Controllers\Admin\Settings\SettingController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\Announcements\AnnouncementController;
use App\Http\Controllers\Admin\Community\CommunityController;
use App\Http\Controllers\Admin\EmergencyContacts\EmergencyContactController;
use App\Http\Controllers\Admin\Media\MediaController;
use App\Http\Controllers\Admin\Banners\BannerController as AdminBannerController;

// Admin Backend Routes (Session Based)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard-data', [DashboardController::class, 'index']);
        
        // Vendors
        Route::get('/vendors-data', [VendorController::class, 'index']);
        Route::get('/vendors-data/{id}', [VendorController::class, 'show']);
        Route::get('/vendors-data/{id}', [VendorController::class, 'show']);
        Route::delete('/vendors-data/{id}', [VendorController::class, 'destroy']);
        Route::post('/vendors-data/{id}/approve', [VendorController::class, 'approve']);
        Route::post('/vendors-data/{id}/reject', [VendorController::class, 'reject']);
        
        // Audit Logs
        Route::get('/audit-logs-data', [AuditLogController::class, 'index']);
        Route::get('/audit-logs-data/{id}', [AuditLogController::class, 'show']);
        
        // Settings
        Route::get('/settings-data', [SettingController::class, 'index']);
        Route::post('/settings-data', [SettingController::class, 'update']);
        Route::get('/maintenance-status', [SettingController::class, 'maintenanceStatus']);
        Route::put('/settings-data/{key}', [SettingController::class, 'updateSetting']);
        
        // Profile
        Route::post('/profile-data', [ProfileController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user-data', [AuthController::class, 'user']);

        // Announcements (Notifications)
        Route::get('/notifications-data', [AnnouncementController::class, 'index']);
        Route::get('/notifications-data/{id}', [AnnouncementController::class, 'show']);
        Route::post('/notifications-data', [AnnouncementController::class, 'store']);
        Route::put('/notifications-data/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/notifications-data/{id}', [AnnouncementController::class, 'destroy']);

        // Emergency Contacts
        Route::get('/emergency-contacts-data', [EmergencyContactController::class, 'index']);
        Route::get('/emergency-contacts-data/{id}', [EmergencyContactController::class, 'show']);
        Route::post('/emergency-contacts-data', [EmergencyContactController::class, 'store']);
        Route::put('/emergency-contacts-data/{id}', [EmergencyContactController::class, 'update']);
        Route::delete('/emergency-contacts-data/{id}', [EmergencyContactController::class, 'destroy']);

        // Media
        Route::post('/media', [MediaController::class, 'store']);
        Route::post('/media/multiple', [MediaController::class, 'storeMultiple']);
        Route::get('/media', [MediaController::class, 'getModelMedia']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
        
        // Banners
        Route::get('/banners-data', [AdminBannerController::class, 'index']);
        Route::get('/banners-data/{id}', [AdminBannerController::class, 'show']);
        Route::post('/banners-data', [AdminBannerController::class, 'store']);
        Route::put('/banners-data/{id}', [AdminBannerController::class, 'update']);
        Route::delete('/banners-data/{id}', [AdminBannerController::class, 'destroy']);
        
        // Contact Directory (Community)
        Route::get('/contacts-directory-data', [CommunityController::class, 'index']);
        Route::get('/contacts-directory-data/{id}', [CommunityController::class, 'show']);
        Route::post('/contacts-directory-data', [CommunityController::class, 'store']);
        Route::put('/contacts-directory-data/{id}', [CommunityController::class, 'update']);
        Route::delete('/contacts-directory-data/{id}', [CommunityController::class, 'destroy']);
        Route::get('/contact-logs-data', [CommunityController::class, 'auditLogs']);
        
        // Enhanced contact directory endpoints
        Route::get('/contacts-directory-data/by-tag/{tagSlug}', [CommunityController::class, 'getByTagSlug']); 
        Route::get('/contacts-directory-data/tags/available', [CommunityController::class, 'getTags']);
        Route::get('/contacts-directory-data/stats/overview', [CommunityController::class, 'getStats']); 
        Route::post('/contacts-directory-data/bulk/status', [CommunityController::class, 'bulkUpdateStatus']); 
        Route::post('/contacts-directory-data/export', [CommunityController::class, 'export']); 
    });
});

// SPA Catch-all
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
