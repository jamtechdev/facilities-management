<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FcmTokenController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
});
