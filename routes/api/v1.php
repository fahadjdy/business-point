<?php
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\VendorController;
use App\Http\Controllers\Api\V1\ShopController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\BarberController;
use App\Http\Controllers\Api\V1\ShopProductController;
use App\Http\Controllers\Api\V1\ShopProductCategoryController;
use App\Http\Controllers\Api\V1\ContactBookController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\EmergencyContactController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\MediaController;

// User Auth Routes - Protected by API Key
Route::middleware('api_key')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:login');
});

// Protected User Routes (Specialized features for users)
Route::middleware(['auth:sanctum', 'api_key'])->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update']);

    // User specialized routes (e.g., adding their own shops/products)
    Route::post('/shops', [ShopController::class, 'store']);
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::post('/barbers', [BarberController::class, 'store']);
    Route::post('/shop-product-categories', [ShopProductCategoryController::class, 'store']); // Renamed
    Route::post('/shop-products', [ShopProductController::class, 'store']);
    Route::put('/shop-products/{id}', [ShopProductController::class, 'update']);
    Route::delete('/shop-products/{id}', [ShopProductController::class, 'destroy']);
    Route::post('/contacts', [ContactBookController::class, 'store']);
    Route::post('/banners', [BannerController::class, 'store']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::post('/media/multiple', [MediaController::class, 'storeMultiple']);
    Route::get('/media', [MediaController::class, 'getModelMedia']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);

    // Contact Directory
    Route::get('/contacts-directory', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'index']);
    Route::get('/contacts-directory/{id}', [\App\Http\Controllers\Api\V1\ContactBookController::class, 'show']);

    // Register Business
    Route::get('/register-business/status', [\App\Http\Controllers\Api\V1\VendorRegistrationController::class, 'checkStatus']);
    Route::post('/register-business', [\App\Http\Controllers\Api\V1\VendorRegistrationController::class, 'store']);
    // Vendor Management
    Route::get('/my-business', [VendorController::class, 'myBusiness']);
    Route::put('/my-business', [VendorController::class, 'update']);
});

// Public routes (Guest user view website)
Route::middleware('api_key')->group(function () {
    Route::get('/vendors', [VendorController::class, 'index']);
    Route::get('/vendors/{id}', [VendorController::class, 'show']);
    Route::get('/shop-product-categories', [ShopProductCategoryController::class, 'index']); // Renamed
    Route::get('/shop-categories', [\App\Http\Controllers\Api\V1\ShopCategoryController::class, 'index']); // New Global Categories
    Route::get('/shop-products', [ShopProductController::class, 'index']);
    // Contact Directory (Legacy endpoints for backward compatibility)
    Route::get('/contacts', [ContactBookController::class, 'index']);
    Route::get('/contacts/export', [ContactBookController::class, 'export']);
    Route::get('/contacts/search', [ContactBookController::class, 'search']);
    Route::get('/contacts/tags/available', [ContactBookController::class, 'getTags']);
    Route::get('/contacts/by-tag/{tagSlug}', [ContactBookController::class, 'getByTagSlug']);
    Route::get('/contacts/{id}', [ContactBookController::class, 'show']);
    
    // Community Directory (New preferred endpoints)
    Route::get('/community-directory', [ContactBookController::class, 'index']);
    Route::get('/community-directory/export', [ContactBookController::class, 'export']);
    Route::get('/community-directory/search', [ContactBookController::class, 'search']);
    Route::get('/community-directory/tags/available', [ContactBookController::class, 'getTags']);
    Route::get('/community-directory/by-tag/{tagSlug}', [ContactBookController::class, 'getByTagSlug']);
    Route::get('/community-directory/{id}', [ContactBookController::class, 'show']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::get('/emergency-contacts', [EmergencyContactController::class, 'index']);
    Route::get('/company-details', [SettingController::class, 'index']);
    Route::get('/maintenance-status', [SettingController::class, 'maintenanceStatus']);
    Route::get('/tags', [App\Http\Controllers\Api\V1\TagController::class, 'index']);
    Route::get('/tags/all', [App\Http\Controllers\Api\V1\TagController::class, 'all']);
    Route::get('/tags/grouped', [App\Http\Controllers\Api\V1\TagController::class, 'grouped']);
    Route::get('/tags/popular', [App\Http\Controllers\Api\V1\TagController::class, 'popular']);
    Route::get('/tags/stats', [App\Http\Controllers\Api\V1\TagController::class, 'stats']);
});

require __DIR__ . '/v1/user.php';

