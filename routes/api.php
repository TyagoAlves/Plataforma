<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/processes/receive', [ApiController::class, 'receiveProcess']);
Route::get('/processes/{process}', [ApiController::class, 'getProcess']);
Route::post('/processes/{process}/response', [ApiController::class, 'submitResponse']);
