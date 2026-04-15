<?php

use App\Http\Controllers\Api\V1\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'create']);
});
