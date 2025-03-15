<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('tasks', TaskController::class)->middleware('auth');

Auth::routes();

Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
