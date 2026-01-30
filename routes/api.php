<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1.php';

    // Admin Routes moved to web.php

});
