<?php

use App\Http\Controllers\Api\V1\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'list']);
    Route::post('/', [BookingController::class, 'create']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::post('/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/{id}/reschedule', [BookingController::class, 'reschedule']);
});
