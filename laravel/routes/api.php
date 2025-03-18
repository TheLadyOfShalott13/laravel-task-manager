<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTaskController;

Route::post('/generate_token', function (Request $request) {
    return (new App\Http\Controllers\ApiTaskController)->generate_token($request);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', ApiTaskController::class);
});
