<?php

use App\Http\Controllers\Api\V1\BarbershopController;
use Illuminate\Support\Facades\Route;

Route::prefix('barbershops')->group(function () {
    Route::get('/', [BarbershopController::class, 'list']);
    Route::get('/{slug}', [BarbershopController::class, 'show']);
});
