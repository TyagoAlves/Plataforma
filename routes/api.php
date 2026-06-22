<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/processes/receive', [ApiController::class, 'receiveProcess']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/processes/{process}', [ApiController::class, 'getProcess']);
    Route::post('/processes/{process}/response', [ApiController::class, 'submitResponse']);
});
