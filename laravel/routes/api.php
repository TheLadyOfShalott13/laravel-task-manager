<?php

use App\Http\Controllers\ApiTaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', ApiTaskController::class);
});
