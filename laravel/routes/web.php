<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', function () { return view('welcome'); });
Route::resource('tasks', TaskController::class)->middleware('auth');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
