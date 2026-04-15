<?php

use App\Http\Controllers\Api\V1\OwnerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard']);
});
