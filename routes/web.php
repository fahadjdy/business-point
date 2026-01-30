<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProfileController;

// Admin Backend Routes (Session Based)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard-data', [DashboardController::class, 'index']);
        Route::get('/vendors-data', [VendorController::class, 'index']);
        Route::get('/vendors-data/{id}', [VendorController::class, 'show']);
        Route::delete('/vendors-data/{id}', [VendorController::class, 'destroy']);
        Route::get('/audit-logs-data', [AuditLogController::class, 'index']);
        Route::get('/audit-logs-data/{id}', [AuditLogController::class, 'show']);
        Route::get('/settings-data', [SettingController::class, 'index']);
        Route::post('/settings-data', [SettingController::class, 'update']);
        Route::get('/maintenance-status', [SettingController::class, 'maintenanceStatus']);
        Route::put('/settings-data/{key}', [SettingController::class, 'updateSetting']);
        Route::post('/profile-data', [ProfileController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user-data', [AuthController::class, 'user']);

        // Announcements (Notifications)
        Route::get('/notifications-data', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::get('/notifications-data/{id}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'show']);
        Route::post('/notifications-data', [\App\Http\Controllers\Api\V1\NotificationController::class, 'store']);
        Route::put('/notifications-data/{id}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'update']);
        Route::delete('/notifications-data/{id}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);

        // Emergency Contacts
        Route::get('/emergency-contacts-data', [\App\Http\Controllers\Api\V1\EmergencyContactController::class, 'index']);
        Route::get('/emergency-contacts-data/{id}', [\App\Http\Controllers\Api\V1\EmergencyContactController::class, 'show']);
        Route::post('/emergency-contacts-data', [\App\Http\Controllers\Api\V1\EmergencyContactController::class, 'store']);
        Route::put('/emergency-contacts-data/{id}', [\App\Http\Controllers\Api\V1\EmergencyContactController::class, 'update']);
        Route::delete('/emergency-contacts-data/{id}', [\App\Http\Controllers\Api\V1\EmergencyContactController::class, 'destroy']);

        // Media
        Route::post('/media', [\App\Http\Controllers\Api\V1\MediaController::class, 'store']);
        Route::post('/media/multiple', [\App\Http\Controllers\Api\V1\MediaController::class, 'storeMultiple']);
        Route::get('/media', [\App\Http\Controllers\Api\V1\MediaController::class, 'getModelMedia']);
        Route::delete('/media/{id}', [\App\Http\Controllers\Api\V1\MediaController::class, 'destroy']);
        // Contact Directory
        // Contact Directory
        Route::get('/contacts-directory-data', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'index']);
        Route::get('/contacts-directory-data/{id}', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'show']);
        Route::post('/contacts-directory-data', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'store']);
        Route::put('/contacts-directory-data/{id}', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'update']);
        Route::delete('/contacts-directory-data/{id}', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'destroy']);
        Route::get('/contact-logs-data', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'auditLogs']);
        
        // Enhanced contact directory endpoints
        Route::get('/contacts-directory-data/by-tag/{tagSlug}', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'getByTagSlug']);
        Route::get('/contacts-directory-data/tags/available', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'getTags']);
        Route::get('/contacts-directory-data/stats/overview', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'getStats']);
        Route::post('/contacts-directory-data/bulk/status', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'bulkUpdateStatus']);
        Route::post('/contacts-directory-data/export', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'export']);
    });
});

// SPA Catch-all
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
