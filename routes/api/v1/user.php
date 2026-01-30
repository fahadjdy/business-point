<?php

use App\Http\Controllers\Api\V1\UserSettingController;
use App\Http\Controllers\Admin\ProfileController;

Route::middleware(['auth:sanctum', 'api_key'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/user/settings', [UserSettingController::class, 'index']);
    Route::post('/user/settings', [UserSettingController::class, 'update']);
});
