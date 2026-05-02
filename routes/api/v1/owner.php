<?php

use App\Http\Controllers\Api\V1\OwnerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard']);
    Route::get('/calendar', [OwnerController::class, 'calendar']);
    Route::get('/analytics', [OwnerController::class, 'analytics']);

    Route::get('/bookings', [OwnerController::class, 'bookings']);
    Route::post('/bookings/{id}/cancel', [OwnerController::class, 'cancelBooking']);
    Route::post('/bookings/{id}/complete', [OwnerController::class, 'completeBooking']);

    Route::get('/services', [OwnerController::class, 'listServices']);
    Route::post('/services', [OwnerController::class, 'createService']);
    Route::put('/services/{id}', [OwnerController::class, 'updateService']);
    Route::delete('/services/{id}', [OwnerController::class, 'deleteService']);
});
