<?php

use App\Http\Controllers\ApiTaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', ApiTaskController::class);
