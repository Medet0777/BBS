<?php

use App\Http\Controllers\Api\V1\BarbershopController;
use App\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('barbershops')->group(function () {
    Route::get('/', [BarbershopController::class, 'list']);
    Route::get('/{slug}', [BarbershopController::class, 'show']);
    Route::get('/{slug}/available-slots', [BarbershopController::class, 'availableSlots']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{slug}/reviews', [ReviewController::class, 'create']);
    });
});
